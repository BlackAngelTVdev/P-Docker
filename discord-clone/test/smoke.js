"use strict";

/**
 * Smoke test for the Cord server.
 *
 * Usage:
 *   node test/smoke.js protocol   # two clients: chat, presence, voice, signaling
 *   node test/smoke.js history    # after a restart: the message must still be there
 *
 * Expects a server already listening on PORT (default 555).
 */

const WebSocket = require("ws");
const PORT = process.env.PORT || 555;
const URL = `ws://localhost:${PORT}/ws`;
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

let failures = 0;
function check(label, ok) {
  console.log(`${ok ? "PASS" : "FAIL"}  ${label}`);
  if (!ok) failures++;
}

function client(nickname) {
  const ws = new WebSocket(URL);
  const inbox = [];
  ws.on("message", (d) => inbox.push(JSON.parse(d.toString())));
  const opened = new Promise((r) => ws.on("open", r));
  return { ws, inbox, opened };
}

async function testProtocol() {
  const A = client("Alice");
  const B = client("Bob");
  await Promise.all([A.opened, B.opened]);

  A.ws.send(JSON.stringify({ type: "hello", id: "user-alice", nickname: "Alice" }));
  await wait(250);
  const welcome = A.inbox.find((m) => m.type === "welcome");
  check("welcome contains 5 channels", welcome && welcome.channels.length === 5);

  B.ws.send(JSON.stringify({ type: "hello", id: "user-bob", nickname: "Bob" }));
  await wait(300);
  const presB = B.inbox.filter((m) => m.type === "presence").at(-1);
  check(
    "presence lists Alice + Bob",
    presB && presB.users.length === 2 && presB.users.every((u) => ["Alice", "Bob"].includes(u.nickname))
  );

  A.inbox.length = 0;
  A.ws.send(JSON.stringify({ type: "history", channelId: "general" }));
  await wait(200);
  const hist = A.inbox.find((m) => m.type === "history");
  check("empty history returned", hist && Array.isArray(hist.messages));

  A.inbox.length = 0;
  B.inbox.length = 0;
  A.ws.send(JSON.stringify({ type: "message", channelId: "general", text: "bonjour <script>alert(1)</script>" }));
  await wait(300);
  const ownMsg = A.inbox.find((m) => m.type === "message");
  const bMsg = B.inbox.find((m) => m.type === "message");
  check("sender sees own message", Boolean(ownMsg));
  check("other client receives broadcast", Boolean(bMsg));
  check("message text kept raw (XSS escaped client-side)", bMsg && bMsg.message.text === "bonjour <script>alert(1)</script>");

  A.ws.send(JSON.stringify({ type: "voice:join", channelId: "vocal-general" }));
  await wait(200);
  B.ws.send(JSON.stringify({ type: "voice:join", channelId: "vocal-general" }));
  await wait(300);
  const presVoice = B.inbox.filter((m) => m.type === "presence").at(-1);
  const vocal = presVoice.users.filter((u) => u.voiceChannel === "vocal-general");
  check("both users in voice channel", vocal.length === 2);

  B.ws.send(JSON.stringify({ type: "speaking", speaking: true }));
  await wait(300);
  const spk = A.inbox.find((m) => m.type === "speaking");
  check("speaking relayed to channel member", spk && spk.userId === "user-bob" && spk.speaking === true);

  A.inbox.length = 0;
  A.ws.send(JSON.stringify({ type: "signal", target: "user-bob", data: { sdp: { type: "offer", sdp: "fake-sdp" } } }));
  await wait(250);
  const sig = B.inbox.find((m) => m.type === "signal");
  check("SDP relayed between voice members", sig && sig.from === "user-alice" && sig.data.sdp.type === "offer");

  A.ws.send(JSON.stringify({ type: "voice:leave" }));
  await wait(250);
  B.inbox.length = 0;
  A.ws.send(JSON.stringify({ type: "signal", target: "user-bob", data: { sdp: { type: "offer", sdp: "x" } } }));
  await wait(250);
  check("signal NOT relayed after leaving voice", B.inbox.filter((m) => m.type === "signal").length === 0);

  A.inbox.length = 0;
  A.ws.send(JSON.stringify({ type: "message", channelId: "vocal-general", text: "nope" }));
  A.ws.send(JSON.stringify({ type: "message", channelId: "general", text: "x".repeat(5000) }));
  await wait(250);
  check("messages to voice channel / >2000 chars rejected", A.inbox.filter((m) => m.type === "message").length === 0);

  B.ws.send(JSON.stringify({ type: "rename", nickname: "Bobby" }));
  await wait(250);
  const presRename = A.inbox.filter((m) => m.type === "presence").at(-1);
  check("rename propagated", presRename && presRename.users.find((u) => u.id === "user-bob").nickname === "Bobby");

  A.ws.close();
  B.ws.close();
}

async function testHistory() {
  const C = client("Carol");
  await C.opened;
  C.ws.send(JSON.stringify({ type: "hello", id: "user-carol", nickname: "Carol" }));
  await wait(200);
  C.ws.send(JSON.stringify({ type: "history", channelId: "general" }));
  await wait(300);
  const hist = C.inbox.find((m) => m.type === "history");
  const ok = hist && hist.messages.length === 1 && hist.messages[0].text === "bonjour <script>alert(1)</script>";
  check("history survived restart (1 message, text intact)", ok);
  C.ws.close();
}

(async () => {
  const mode = process.argv[2] || "protocol";
  if (mode === "protocol") await testProtocol();
  else if (mode === "history") await testHistory();
  else throw new Error(`unknown mode: ${mode}`);
  console.log(failures ? `\n${failures} check(s) FAILED` : "\nAll checks passed ✔");
  process.exit(failures ? 1 : 0);
})().catch((e) => {
  console.error("Smoke test crashed:", e);
  process.exit(1);
});
