# Cord — clone de Discord (texte + vocal)

Un petit Discord-like auto-hébergé : salons texte en temps réel, présence en
ligne, et salons vocaux en peer-to-peer (WebRTC). L'historique des messages
est conservé dans un fichier JSON et survit aux redémarrages.

Le serveur écoute sur le port **555** par défaut.

## Démarrage

```bash
cd discord-clone
npm install
npm start
```

Puis ouvre http://localhost:555 dans un navigateur.

- Choisis un pseudo → rejoins le serveur.
- Clique sur un salon texte (`# général`, `# dev`…) pour chatter.
- Clique sur un salon vocal (`🔊 Vocal Général`…) pour parler avec le micro.
  Les participants connectés au même vocal s'entendent directement (maillage WebRTC).
- `Micro activé/coupé` mute le micro, `Quitter le vocal` raccroche.

## Déploiement Docker

### Option A — autonome (recommandé pour démarrer)

```bash
cd discord-clone
docker compose up -d --build
# → http://localhost:555   (historique conservé dans le volume chat_data)
```

### Option B — dans la stack Swarm de ce dépôt (docker-compose.yml racine)

Un service `chat` y a été ajouté en suivant les conventions de la stack
(placement sur le nœud manager .135, réseau `frontend`, volume local + port 555 publié).

```bash
# 1. Construire l'image (docker stack deploy ignore la directive build)
docker compose build chat
# 2. Redéployer la stack (les secrets/réseaux externes sont déjà en place)
docker stack deploy -c docker-compose.yml <nom-de-la-stack>
# → http://192.168.88.135:555
```

> Nota : `docker run` équivalent — `docker run -d --name cord-chat -p 555:555 \
>   --cap-add NET_BIND_SERVICE -v cord-chat-data:/app/data cord-chat:latest`.
> Le `cap_add NET_BIND_SERVICE` permet à l'utilisateur non-root `node` de binder
> le port privilégié 555 ; sans lui, lancer en root ou utiliser un port ≥ 1024.

## Reverse proxy nginx (GitLab + chat derrière le port 80)

Si vous avez aussi GitLab sur le serveur et que seuls les ports 80/443 sont
accessibles depuis l'extérieur, servez les deux applications via nginx :
voir [`nginx/README.md`](nginx/README.md).

## Configuration

| Variable | Défaut | Rôle |
| --- | --- | --- |
| `PORT` | `555` | Port d'écoute HTTP + WebSocket |
| `HOST` | `0.0.0.0` | Interface d'écoute |

Les salons sont définis en haut de `server.js` (tableau `CHANNELS`).

## Architecture

- `server.js` — serveur Node sans framework (`http` + `ws`) : fichiers statiques,
  API WebSocket JSON sur `/ws` (chat, présence, relais de signalisation WebRTC),
  persistance JSON débouncée dans `data/db.json` (300 derniers messages par salon).
- `public/` — client : `index.html`, `styles.css`, `app.js`.
- Voix : chaque client publie son flux audio et négocie des connexions
  peer-to-peer directes (« perfect negotiation ») via le serveur qui relaie
  uniquement les SDP/ICE. Indicateur « en train de parler » via analyse du volume.

## Remarques sur le vocal

- Le micro n'est accessible qu'en **contexte sécurisé** : `http://localhost`
  fonctionne, mais pour y accéder depuis une autre machine en HTTP pur, le
  navigateur bloquera le micro (utilise `https://` ou un tunnel).
- Les serveurs STUN publics (Google) couvrent la plupart des NAT. Pour les
  réseaux très restrictifs ou l'accès Internet, ajoute un serveur TURN dans la
  constante `ICE_SERVERS` en haut de `public/app.js`.
- Topologie maillée : excellent pour quelques interlocuteurs ; au-delà,
  un SFU serait nécessaire.

## Endpoints

- `GET /` — interface web
- `GET /api/health` — état + liste des utilisateurs connectés
- `WS  /ws` — protocole JSON du client
