"use strict";

/* ------------------------------------------------------------------ *
 *  Cord — Discord-like client (chat + WebRTC voice)
 *  Talks JSON over WebSocket to /ws; voice is peer-to-peer with the
 *  server acting as a signaling relay.
 * ------------------------------------------------------------------ */

// Public STUN servers used for NAT traversal. For connections that need
// more than STUN (e.g. strict NAT / internet), add a TURN server here.
const ICE_SERVERS = [
  { urls: "stun:stun.l.google.com:19302" },
  { urls: "stun:stun1.l.google.com:19302" },
];

const $ = (sel) => document.querySelector(sel);

// ---------------- identity (kept across refreshes) ----------------
function getUserId() {
  let id = sessionStorage.getItem("dc_user_id");
  if (!id) {
    id = crypto.randomUUID();
    sessionStorage.setItem("dc_user_id", id);
  }
  return id;
}
const MY_ID = getUserId();

function getNickname() {
  return localStorage.getItem("dc_nickname") || "";
}
function setNickname(name) {
  localStorage.setItem("dc_nickname", name);
}

// ---------------- state ----------------
const state = {
  ws: null,
  reconnectTimer: null,
  closedByUser: false,
  channels: [],
  channelId: "general", // active text channel
  users: new Map(),     // id -> { id, nickname, voiceChannel, speaking }
  messages: [],         // messages of the active text channel
  voiceChannel: null,   // voice channel id we are in (or null)
  voiceChannelName: "",
  localStream: null,
  audioCtx: null,
  analyser: null,
  micTrack: null,
  micMuted: false,
  speaking: false,
  peers: new Map(),     // peerId -> { pc, nickname }
  makingOffer: false,
  speakTimer: null,
  connected: false,
};

// ---------------- helpers ----------------
function escapeHtml(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function hashHue(str) {
  let h = 0;
  for (let i = 0; i < str.length; i++) h = (h * 31 + str.charCodeAt(i)) >>> 0;
  return h % 360;
}

function avatarColor(str) {
  return `hsl(${hashHue(str)} 65% 45%)`;
}

function authorColor(nickname) {
  return `hsl(${hashHue(nickname)} 60% 72%)`;
}

function initials(nickname) {
  const parts = nickname.trim().split(/\s+/);
  const first = parts[0] ? parts[0][0] : "?";
  const second = parts[1] ? parts[1][0] : (parts[0] && parts[0][1]) || "";
  return (first + second).toUpperCase();
}

function fmtTime(ts) {
  return new Date(ts).toLocaleTimeString("fr-FR", { hour: "2-digit", minute: "2-digit" });
}

function wsUrl() {
  const proto = location.protocol === "https:" ? "wss" : "ws";
  return `${proto}://${location.host}/ws`;
}

function send(obj) {
  if (state.ws && state.ws.readyState === WebSocket.OPEN) state.ws.send(JSON.stringify(obj));
}

// ---------------- WebSocket ----------------
function connect() {
  clearTimeout(state.reconnectTimer);
  const ws = new WebSocket(wsUrl());
  state.ws = ws;

  ws.onopen = () => {
    state.connected = true;
    send({ type: "hello", id: MY_ID, nickname: getNickname() });
  };

  ws.onmessage = (ev) => {
    let msg;
    try {
      msg = JSON.parse(ev.data);
    } catch {
      return;
    }
    handleServerMessage(msg);
  };

  ws.onclose = () => {
    state.connected = false;
    if (state.closedByUser) return;
    // Drop any voice state; a reconnect below rejoins if needed.
    teardownVoice();
    state.peers.clear();
    state.users.clear();
    appendSystemLine("Connexion perdue… nouvelle tentative dans 2 s.");
    state.reconnectTimer = setTimeout(() => {
      if (!state.closedByUser) connect();
    }, 2000);
  };

  ws.onerror = () => ws.close();
}

function handleServerMessage(msg) {
  switch (msg.type) {
    case "self": {
      renderSelfCard(msg.self);
      break;
    }

    case "welcome": {
      state.channels = msg.channels || [];
      if (!state.channels.some((c) => c.id === state.channelId)) {
        const firstText = state.channels.find((c) => c.type === "text");
        state.channelId = firstText ? firstText.id : "general";
      }
      renderChannels();
      renderSelfCard(msg.self);
      requestHistory(state.channelId);
      // Rejoin the voice channel we were in before the disconnect.
      if (state.voiceChannel) {
        const ch = state.channels.find((c) => c.id === state.voiceChannel);
        if (ch) enterVoiceChannel(ch.id);
        else state.voiceChannel = null;
      }
      break;
    }

    case "history": {
      if (msg.channelId !== state.channelId) break;
      state.messages = msg.messages || [];
      renderMessages();
      break;
    }

    case "message": {
      if (msg.message.channelId === state.channelId) {
        state.messages.push(msg.message);
        appendMessage(msg.message);
      }
      break;
    }

    case "presence": {
      state.users = new Map((msg.users || []).map((u) => [u.id, u]));
      renderChannels();
      renderMembers();
      syncPeers();
      break;
    }

    case "speaking": {
      const user = state.users.get(msg.userId);
      if (user) {
        user.speaking = Boolean(msg.speaking);
        setSpeakingUI(msg.userId, user.speaking);
      }
      break;
    }

    case "signal": {
      handleSignal(msg.from, msg.data);
      break;
    }
  }
}

// ---------------- sidebar (channels) ----------------
function channelRow(ch) {
  if (ch.type === "text") {
    const div = document.createElement("div");
    div.className = "channel" + (ch.id === state.channelId ? " active" : "");
    div.dataset.channel = ch.id;
    div.dataset.type = "text";
    div.innerHTML = `<span class="icon">#</span><span class="name">${escapeHtml(ch.name)}</span>`;
    return div;
  }
  // voice channel row + occupants
  const wrap = document.createElement("div");
  const row = document.createElement("div");
  row.className = "channel" + (ch.id === state.voiceChannel ? " active" : "");
  row.dataset.channel = ch.id;
  row.dataset.type = "voice";
  const occupants = [...state.users.values()].filter((u) => u.voiceChannel === ch.id);
  row.innerHTML = `<span class="icon">🔊</span><span class="name">${escapeHtml(ch.name)}</span>` +
    (occupants.length ? `<span class="badge">${occupants.length}</span>` : "");
  wrap.appendChild(row);

  if (occupants.length) {
    const occ = document.createElement("div");
    occ.className = "voice-occupants";
    occ.dataset.occupants = ch.id;
    for (const u of occupants) occ.appendChild(occupantRow(u));
    wrap.appendChild(occ);
  }
  return wrap;
}

function occupantRow(u) {
  const div = document.createElement("div");
  div.className = "occ" + (u.speaking ? " speaking" : "");
  div.dataset.user = u.id;
  div.innerHTML = `<span class="dot"></span><span class="name">${escapeHtml(u.nickname)}</span>`;
  return div;
}

function renderChannels() {
  const list = $("#channel-list");
  list.innerHTML = "";
  const text = state.channels.filter((c) => c.type === "text");
  const voice = state.channels.filter((c) => c.type === "voice");
  if (text.length) {
    list.appendChild(groupLabel("Salons texte"));
    for (const ch of text) list.appendChild(channelRow(ch));
  }
  if (voice.length) {
    list.appendChild(groupLabel("Salons vocaux"));
    for (const ch of voice) list.appendChild(channelRow(ch));
  }
}

function groupLabel(text) {
  const div = document.createElement("div");
  div.className = "ch-group-label";
  div.textContent = text;
  return div;
}

$("#channel-list").addEventListener("click", (ev) => {
  const row = ev.target.closest(".channel");
  if (!row) return;
  const chId = row.dataset.channel;
  const type = row.dataset.type;
  if (type === "text") switchChannel(chId);
  else if (type === "voice") {
    if (state.voiceChannel === chId) leaveVoiceChannel();
    else enterVoiceChannel(chId);
  }
});

function switchChannel(chId) {
  if (chId === state.channelId) return;
  state.channelId = chId;
  state.messages = [];
  document.querySelectorAll("#channel-list .channel").forEach((el) => {
    el.classList.toggle("active", el.dataset.channel === chId && el.dataset.type === "text");
  });
  updateChatHeader();
  requestHistory(chId);
}

function requestHistory(channelId) {
  send({ type: "history", channelId });
}

function updateChatHeader() {
  const ch = state.channels.find((c) => c.id === state.channelId);
  if (!ch) return;
  $("#channel-title").textContent = "# " + ch.name;
  $("#composer-input").placeholder = `Écrire dans #${ch.name}`;
  $("#channel-topic").textContent = "";
}

// ---------------- messages ----------------
function renderMessages() {
  const box = $("#messages");
  box.innerHTML = "";
  if (!state.messages.length) {
    const empty = document.createElement("div");
    empty.className = "empty";
    empty.textContent = "Aucun message pour l'instant. Sois le premier à dire bonjour 👋";
    box.appendChild(empty);
    return;
  }
  for (const m of state.messages) box.appendChild(messageNode(m));
  scrollToBottom(true);
}

function appendMessage(m) {
  $("#messages .empty")?.remove();
  $("#messages").appendChild(messageNode(m));
  scrollToBottom();
}

function messageNode(m) {
  const wrap = document.createElement("div");
  wrap.className = "msg";
  const initialsTxt = initials(m.nickname || "?");
  const avatarBg = avatarColor(m.userId || m.nickname);
  wrap.innerHTML = `
    <div class="msg-avatar" style="background:${avatarBg}">${escapeHtml(initialsTxt)}</div>
    <div class="body">
      <div class="head">
        <span class="author" style="color:${authorColor(m.nickname)}">${escapeHtml(m.nickname)}</span>
        <span class="time">${fmtTime(m.ts)}</span>
      </div>
      <div class="text">${escapeHtml(m.text)}</div>
    </div>`;
  return wrap;
}

function appendSystemLine(text) {
  const box = $("#messages");
  const wrap = document.createElement("div");
  wrap.className = "msg system";
  wrap.innerHTML = `<div class="body"><div class="text">${escapeHtml(text)}</div></div>`;
  box.appendChild(wrap);
  scrollToBottom();
}

function scrollToBottom(force) {
  const box = $("#messages");
  const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 120;
  if (force || nearBottom) box.scrollTop = box.scrollHeight;
}

$("#composer").addEventListener("submit", (ev) => {
  ev.preventDefault();
  const input = $("#composer-input");
  const text = input.value.trim();
  if (!text) return;
  send({ type: "message", channelId: state.channelId, text });
  input.value = "";
  input.focus();
});

// ---------------- members sidebar ----------------
function renderMembers() {
  const box = $("#members");
  box.innerHTML = "";
  const all = [...state.users.values()].sort((a, b) => a.nickname.localeCompare(b.nickname));
  const inVoice = all.filter((u) => u.voiceChannel);
  const others = all.filter((u) => !u.voiceChannel);

  if (inVoice.length) {
    box.appendChild(groupLabel(`En vocal — ${inVoice.length}`));
    for (const u of inVoice) box.appendChild(memberNode(u, true));
  }
  if (others.length) {
    box.appendChild(groupLabel(`En ligne — ${others.length}`));
    for (const u of others) box.appendChild(memberNode(u, false));
  }
}

function memberNode(u, inVoice) {
  const div = document.createElement("div");
  div.className = "member";
  div.dataset.user = u.id;
  const ch = inVoice ? state.channels.find((c) => c.id === u.voiceChannel) : null;
  div.innerHTML = `
    <div class="avatar" style="background:${avatarColor(u.id)}">${escapeHtml(initials(u.nickname))}</div>
    <div class="meta">
      <div class="name">${escapeHtml(u.nickname)}${u.id === MY_ID ? " (toi)" : ""}</div>
      <div class="status">${inVoice ? `🔊 ${escapeHtml(ch ? ch.name : "vocal")}` : "En ligne"}</div>
    </div>`;
  if (u.speaking) div.classList.add("speaking");
  return div;
}

function setSpeakingUI(userId, speaking) {
  document.querySelectorAll(`[data-user="${userId}"]`).forEach((el) => {
    el.classList.toggle("speaking", speaking);
    if (el.classList.contains("occ")) {
      el.querySelector(".dot").style.boxShadow = speaking ? "0 0 6px var(--green)" : "";
    }
    if (el.classList.contains("member")) {
      el.querySelector(".avatar").style.boxShadow = speaking
        ? "0 0 0 2px var(--green)"
        : "";
    }
  });
}

// ---------------- self card ----------------
function renderSelfCard(self) {
  const nameEl = $("#self-name");
  nameEl.textContent = self.nickname;
  nameEl.style.color = authorColor(self.nickname);
  const avatar = $("#self-avatar");
  avatar.textContent = initials(self.nickname);
  avatar.style.background = avatarColor(self.id);
}

$("#change-name").addEventListener("click", () => openNameDialog(true));

// ---------------- nickname dialog ----------------
function openNameDialog(force) {
  const overlay = $("#overlay");
  if (!force && getNickname()) return;
  overlay.classList.remove("hidden");
  const input = $("#name-input");
  input.value = getNickname();
  input.focus();
}

$("#name-form").addEventListener("submit", (ev) => {
  ev.preventDefault();
  const input = $("#name-input");
  const name = input.value.trim().slice(0, 32);
  if (!name) return;
  setNickname(name);
  $("#overlay").classList.add("hidden");
  if (state.connected) {
    send({ type: "rename", nickname: name }); // live rename
  } else {
    connect();
  }
});

// ---------------- voice (WebRTC) ----------------
function ensureVoiceBar() {
  let bar = $("#voice-bar");
  if (bar) return bar;
  bar = document.createElement("div");
  bar.id = "voice-bar";
  bar.className = "hidden";
  bar.innerHTML = `
    <div class="state">En vocal — <b id="voice-channel-name"></b></div>
    <div id="voice-controls">
      <button class="mic" id="mic-btn">Micro activé</button>
      <button class="leave" id="leave-btn">Quitter le vocal</button>
    </div>`;
  $("#self-card").before(bar);

  $("#mic-btn").addEventListener("click", toggleMic);
  $("#leave-btn").addEventListener("click", leaveVoiceChannel);
  return bar;
}

function setVoiceBarVisible(visible) {
  const bar = ensureVoiceBar();
  bar.classList.toggle("hidden", !visible);
  if (visible) $("#voice-channel-name").textContent = state.voiceChannelName;
}

async function enterVoiceChannel(channelId) {
  const ch = state.channels.find((c) => c.id === channelId);
  if (!ch || ch.type !== "voice") return;

  if (!state.localStream) {
    try {
      state.localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
    } catch (err) {
      appendSystemLine(`Micro inaccessible (${err.name}) — autorise l'accès au micro pour le vocal.`);
      return;
    }
    state.micTrack = state.localStream.getAudioTracks()[0] || null;
    state.micMuted = false;
    setupSpeakingDetection();
  }

  // Mute the mic if it was muted while away.
  if (state.micTrack) state.micTrack.enabled = !state.micMuted;
  updateMicButton();

  state.voiceChannel = channelId;
  state.voiceChannelName = ch.name;
  setVoiceBarVisible(true);
  send({ type: "voice:join", channelId });
  // Presence echo from the server triggers syncPeers().
}

function leaveVoiceChannel() {
  if (!state.voiceChannel) return;
  state.voiceChannel = null;
  state.voiceChannelName = "";
  setVoiceBarVisible(false);
  send({ type: "voice:leave" });
  closeAllPeers();
}

function toggleMic() {
  if (!state.micTrack) return;
  state.micMuted = !state.micMuted;
  state.micTrack.enabled = !state.micMuted;
  updateMicButton();
}

function updateMicButton() {
  const btn = $("#mic-btn");
  if (!btn) return;
  btn.textContent = state.micMuted ? "Micro coupé" : "Micro activé";
  btn.classList.toggle("muted", state.micMuted);
}

function teardownVoice() {
  // Stops media + analyser but keeps state.voiceChannel so we can rejoin.
  if (state.localStream) {
    state.localStream.getTracks().forEach((t) => t.stop());
    state.localStream = null;
    state.micTrack = null;
  }
  if (state.audioCtx) {
    state.audioCtx.close().catch(() => {});
    state.audioCtx = null;
    state.analyser = null;
  }
  clearInterval(state.speakTimer);
  state.speakTimer = null;
  state.speaking = false;
  closeAllPeers();
}

function setupSpeakingDetection() {
  const AudioCtx = window.AudioContext || window.webkitAudioContext;
  if (!AudioCtx) return;
  state.audioCtx = new AudioCtx();
  state.analyser = state.audioCtx.createAnalyser();
  state.analyser.fftSize = 2048;
  const source = state.audioCtx.createMediaStreamSource(state.localStream);
  source.connect(state.analyser);
  // Don't route the mic back to the speakers (no feedback loop).

  const buf = new Float32Array(state.analyser.fftSize);
  const on = 0.045; // RMS above this => speaking
  const off = 0.012; // RMS below this => silent (hysteresis)

  state.speakTimer = setInterval(() => {
    if (!state.analyser || state.micMuted || !state.voiceChannel) return;
    state.analyser.getFloatTimeDomainData(buf);
    let sum = 0;
    for (let i = 0; i < buf.length; i++) sum += buf[i] * buf[i];
    const rms = Math.sqrt(sum / buf.length);
    if (rms >= on && !state.speaking) setSpeaking(true);
    else if (rms < off && state.speaking) setSpeaking(false);
  }, 150);
}

function setSpeaking(speaking) {
  state.speaking = speaking;
  send({ type: "speaking", speaking });
}

// --- perfect-negotiation WebRTC (mesh) ---
function syncPeers() {
  if (!state.voiceChannel) {
    closeAllPeers();
    return;
  }
  const occupants = [...state.users.values()].filter(
    (u) => u.voiceChannel === state.voiceChannel && u.id !== MY_ID
  );
  const occupantIds = new Set(occupants.map((u) => u.id));

  for (const u of occupants) {
    if (!state.peers.has(u.id)) createPeer(u.id, u.nickname);
  }
  for (const peerId of state.peers.keys()) {
    if (!occupantIds.has(peerId)) closePeer(peerId);
  }
}

function getOrCreatePeer(peerId) {
  let peer = state.peers.get(peerId);
  if (!peer) {
    peer = createPeer(peerId, "…");
    const u = state.users.get(peerId);
    if (u) peer.nickname = u.nickname;
  }
  return peer;
}

function createPeer(peerId, nickname) {
  const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });
  const peer = { pc, nickname, audio: null };
  state.peers.set(peerId, peer);

  if (state.localStream) {
    for (const track of state.localStream.getTracks()) pc.addTrack(track, state.localStream);
  }

  pc.onicecandidate = (ev) => {
    if (ev.candidate) sendSignal(peerId, { candidate: ev.candidate });
  };

  pc.onnegotiationneeded = async () => {
    try {
      state.makingOffer = true;
      await pc.setLocalDescription();
      sendSignal(peerId, { sdp: pc.localDescription });
    } catch (err) {
      console.error("offer error", err);
    } finally {
      state.makingOffer = false;
    }
  };

  pc.ontrack = (ev) => {
    if (!ev.streams[0]) return;
    if (!peer.audio) {
      const audio = new Audio();
      audio.autoplay = true;
      audio.style.display = "none";
      document.body.appendChild(audio);
      peer.audio = audio;
    }
    peer.audio.srcObject = ev.streams[0];
    peer.audio.play().catch(() => {});
  };

  return peer;
}

function sendSignal(target, data) {
  send({ type: "signal", target, data });
}

async function handleSignal(fromId, data) {
  const peer = getOrCreatePeer(fromId);
  const u = state.users.get(fromId);
  if (u) peer.nickname = u.nickname;
  const pc = peer.pc;

  if (data.sdp) {
    const offerCollision =
      data.sdp.type === "offer" && (state.makingOffer || pc.signalingState !== "stable");
    // Deterministic roles: the higher id is polite, the lower one wins offers.
    const polite = MY_ID > fromId;

    if (offerCollision) {
      if (!polite) return; // we are the impolite peer: keep our offer
      try {
        await pc.setLocalDescription({ type: "rollback" });
      } catch {
        return;
      }
    }

    try {
      await pc.setRemoteDescription(data.sdp);
    } catch (err) {
      console.error("setRemoteDescription error", err);
      return;
    }
    if (data.sdp.type === "offer") {
      try {
        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);
        sendSignal(fromId, { sdp: pc.localDescription });
      } catch (err) {
        console.error("answer error", err);
      }
    }
  } else if (data.candidate) {
    try {
      await pc.addIceCandidate(data.candidate);
    } catch (err) {
      console.error("addIceCandidate error", err);
    }
  }
}

function closePeer(peerId) {
  const peer = state.peers.get(peerId);
  if (!peer) return;
  try {
    peer.pc.close();
  } catch {}
  if (peer.audio) {
    peer.audio.srcObject = null;
    peer.audio.remove();
  }
  state.peers.delete(peerId);
}

function closeAllPeers() {
  for (const peerId of [...state.peers.keys()]) closePeer(peerId);
  state.peers.clear();
}

// ---------------- boot ----------------
window.addEventListener("beforeunload", () => {
  send({ type: "voice:leave" });
});

window.addEventListener("load", () => {
  if (getNickname()) connect();
  else openNameDialog(true);
  $("#composer-input").focus();
});
