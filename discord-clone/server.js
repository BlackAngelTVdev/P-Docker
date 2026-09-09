"use strict";

/**
 * Discord-like chat server.
 * - Serves the static front-end from ./public
 * - Exposes a JSON WebSocket API on /ws (chat, presence, voice signaling)
 * - Persists message history to ./data/db.json (survives restarts)
 *
 * Run:  npm install && npm start   (listens on port 555 by default)
 */

const http = require("http");
const fs = require("fs");
const path = require("path");
const crypto = require("crypto");
const { WebSocketServer } = require("ws");

const PORT = parseInt(process.env.PORT || "555", 10);
const HOST = process.env.HOST || "0.0.0.0";
const PUBLIC_DIR = path.join(__dirname, "public");
const DATA_DIR = path.join(__dirname, "data");
const DB_FILE = path.join(DATA_DIR, "db.json");

const MAX_HISTORY_PER_CHANNEL = 300; // most recent messages kept / returned
const MAX_MESSAGE_LENGTH = 2000;
const MAX_NICKNAME_LENGTH = 32;
const SAVE_DEBOUNCE_MS = 1000;

// --- Channels (Discord calls them "channels"; here one server, many channels) ---
const CHANNELS = [
  { id: "general", name: "général", type: "text" },
  { id: "annonces", name: "annonces", type: "text" },
  { id: "dev", name: "dev", type: "text" },
  { id: "vocal-general", name: "Vocal Général", type: "voice" },
  { id: "vocal-gaming", name: "Vocal Gaming", type: "voice" },
];

const isTextChannel = (id) => CHANNELS.some((c) => c.id === id && c.type === "text");
const isVoiceChannel = (id) => CHANNELS.some((c) => c.id === id && c.type === "voice");

// --- Persistence (message history only) ---
let db = { messages: {} };
let dirty = false;

function loadDb() {
  try {
    const raw = fs.readFileSync(DB_FILE, "utf8");
    const parsed = JSON.parse(raw);
    if (parsed && typeof parsed.messages === "object") db = parsed;
  } catch (err) {
    if (err.code !== "ENOENT") console.error("Could not read db file:", err.message);
  }
}

function saveDb() {
  try {
    fs.mkdirSync(DATA_DIR, { recursive: true });
    const tmp = DB_FILE + ".tmp";
    fs.writeFileSync(tmp, JSON.stringify(db, null, 2));
    fs.renameSync(tmp, DB_FILE);
  } catch (err) {
    console.error("Could not save db file:", err.message);
  }
  dirty = false;
}

function markDirty() {
  dirty = true;
}

function addMessage(channelId, message) {
  if (!db.messages[channelId]) db.messages[channelId] = [];
  const list = db.messages[channelId];
  list.push(message);
  if (list.length > MAX_HISTORY_PER_CHANNEL) list.splice(0, list.length - MAX_HISTORY_PER_CHANNEL);
  markDirty();
}

loadDb();
setInterval(() => {
  if (dirty) saveDb();
}, SAVE_DEBOUNCE_MS);

function shutdown() {
  if (dirty) saveDb();
  process.exit(0);
}
process.on("SIGINT", shutdown);
process.on("SIGTERM", shutdown);

// --- HTTP: static files + tiny health endpoint ---
const MIME = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".js": "text/javascript; charset=utf-8",
  ".json": "application/json; charset=utf-8",
  ".svg": "image/svg+xml",
  ".png": "image/png",
  ".ico": "image/x-icon",
  ".woff2": "font/woff2",
};

function sendJson(res, status, obj) {
  res.writeHead(status, { "Content-Type": "application/json; charset=utf-8" });
  res.end(JSON.stringify(obj));
}

const server = http.createServer((req, res) => {
  const url = new URL(req.url, `http://${req.headers.host || "localhost"}`);

  if (url.pathname === "/api/health") {
    const users = [];
    for (const ws of wss.clients) {
      if (ws.readyState === ws.OPEN && ws.user) users.push({ id: ws.user.id, nickname: ws.user.nickname });
    }
    return sendJson(res, 200, { ok: true, port: PORT, online: users.length, users });
  }

  let pathname;
  try {
    pathname = decodeURIComponent(url.pathname);
  } catch {
    return sendJson(res, 400, { error: "bad path" });
  }
  if (pathname === "/") pathname = "/index.html";

  const filePath = path.normalize(path.join(PUBLIC_DIR, pathname));
  if (!filePath.startsWith(PUBLIC_DIR)) return sendJson(res, 403, { error: "forbidden" });

  fs.readFile(filePath, (err, buf) => {
    if (err) return sendJson(res, 404, { error: "not found" });
    const ext = path.extname(filePath).toLowerCase();
    res.writeHead(200, { "Content-Type": MIME[ext] || "application/octet-stream" });
    res.end(buf);
  });
});

// --- WebSocket API ---
const wss = new WebSocketServer({ server, path: "/ws" });

function sendTo(ws, obj) {
  if (ws.readyState === ws.OPEN) ws.send(JSON.stringify(obj));
}

function broadcast(obj, include) {
  const raw = JSON.stringify(obj);
  for (const ws of wss.clients) {
    if (ws.readyState === ws.OPEN && (!include || include(ws))) ws.send(raw);
  }
}

/** Full presence snapshot: every connected user with their voice channel. */
function presenceSnapshot() {
  const users = [];
  for (const ws of wss.clients) {
    if (ws.readyState === ws.OPEN && ws.user) users.push(ws.user);
  }
  return users;
}

function broadcastPresence() {
  broadcast({ type: "presence", users: presenceSnapshot() });
}

/** Sockets currently connected in the same voice channel as `user`. */
function socketsInVoiceChannel(channelId) {
  const out = [];
  for (const ws of wss.clients) {
    if (ws.readyState === ws.OPEN && ws.user && ws.user.voiceChannel === channelId) out.push(ws);
  }
  return out;
}

function findSocket(userId) {
  for (const ws of wss.clients) {
    if (ws.readyState === ws.OPEN && ws.user && ws.user.id === userId) return ws;
  }
  return null;
}

wss.on("connection", (ws) => {
  ws.user = null;

  ws.on("message", (raw) => {
    let msg;
    try {
      msg = JSON.parse(raw.toString());
    } catch {
      return;
    }
    if (!msg || typeof msg.type !== "string") return;

    switch (msg.type) {
      case "hello": {
        const id = typeof msg.id === "string" && msg.id.length <= 64 ? msg.id : crypto.randomUUID();
        let nickname = typeof msg.nickname === "string" ? msg.nickname.trim() : "";
        nickname = nickname.replace(/[\u0000-\u001f<>]/g, "").slice(0, MAX_NICKNAME_LENGTH);
        if (!nickname) nickname = "Anonyme";
        ws.user = { id, nickname, voiceChannel: null, speaking: false };
        sendTo(ws, { type: "welcome", self: ws.user, channels: CHANNELS });
        broadcastPresence();
        break;
      }

      case "rename": {
        if (!ws.user) break;
        let nickname = typeof msg.nickname === "string" ? msg.nickname.trim() : "";
        nickname = nickname.replace(/[\u0000-\u001f<>]/g, "").slice(0, MAX_NICKNAME_LENGTH);
        if (!nickname) break;
        ws.user.nickname = nickname;
        sendTo(ws, { type: "self", self: ws.user });
        broadcastPresence();
        break;
      }

      case "history": {
        if (!ws.user || !isTextChannel(msg.channelId)) break;
        const list = db.messages[msg.channelId] || [];
        sendTo(ws, { type: "history", channelId: msg.channelId, messages: list.slice(-MAX_HISTORY_PER_CHANNEL) });
        break;
      }

      case "message": {
        if (!ws.user || !isTextChannel(msg.channelId)) break;
        const text = typeof msg.text === "string" ? msg.text.trim() : "";
        if (!text || text.length > MAX_MESSAGE_LENGTH) break;
        const message = {
          id: crypto.randomUUID(),
          channelId: msg.channelId,
          userId: ws.user.id,
          nickname: ws.user.nickname,
          text,
          ts: new Date().toISOString(),
        };
        addMessage(msg.channelId, message);
        broadcast({ type: "message", message });
        break;
      }

      case "voice:join": {
        if (!ws.user || !isVoiceChannel(msg.channelId)) break;
        const previous = ws.user.voiceChannel;
        if (previous === msg.channelId) break;
        ws.user.voiceChannel = msg.channelId;
        ws.user.speaking = false;
        if (previous) broadcast({ type: "speaking", userId: ws.user.id, speaking: false });
        broadcastPresence();
        break;
      }

      case "voice:leave": {
        if (!ws.user || !ws.user.voiceChannel) break;
        ws.user.voiceChannel = null;
        ws.user.speaking = false;
        broadcast({ type: "speaking", userId: ws.user.id, speaking: false });
        broadcastPresence();
        break;
      }

      case "speaking": {
        if (!ws.user || !ws.user.voiceChannel) break;
        const speaking = Boolean(msg.speaking);
        if (ws.user.speaking === speaking) break;
        ws.user.speaking = speaking;
        // Only members of the same voice channel need the live indicator.
        const raw = JSON.stringify({ type: "speaking", userId: ws.user.id, speaking });
        for (const peer of socketsInVoiceChannel(ws.user.voiceChannel)) {
          if (peer !== ws && peer.readyState === peer.OPEN) peer.send(raw);
        }
        break;
      }

      case "signal": {
        // WebRTC signaling relay (SDP / ICE) between members of the same voice channel.
        if (!ws.user || !ws.user.voiceChannel) break;
        const target = findSocket(msg.target);
        if (!target || !target.user || target.user.voiceChannel !== ws.user.voiceChannel) break;
        sendTo(target, { type: "signal", from: ws.user.id, fromNickname: ws.user.nickname, data: msg.data });
        break;
      }

      default:
        break;
    }
  });

  ws.on("close", () => {
    if (ws.user) {
      broadcast({ type: "speaking", userId: ws.user.id, speaking: false });
      ws.user = null;
      broadcastPresence();
    }
  });
});

server.listen(PORT, HOST, () => {
  console.log(`Discord-like server running at http://${HOST}:${PORT}  (WebSocket at /ws)`);
});
