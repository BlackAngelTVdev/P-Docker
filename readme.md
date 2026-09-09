# 🚀 P-Docker

![Stars](https://img.shields.io/github/stars/BlackAngelTVdev/P-Docker?style=for-the-badge&color=yellow)
![Commits](https://img.shields.io/github/commit-activity/m/BlackAngelTVdev/P-Docker?style=for-the-badge&color=blue)
![Issues](https://img.shields.io/github/issues/BlackAngelTVdev/P-Docker?style=for-the-badge&color=orange)
![Forks](https://img.shields.io/github/forks/BlackAngelTVdev/P-Docker?style=for-the-badge&color=808080)
![Last Commit](https://img.shields.io/github/last-commit/BlackAngelTVdev/P-Docker?style=for-the-badge&color=blue)

> **Une stack Docker Swarm complète (WordPress + MariaDB + Nginx + Portainer + Chat maison) déployée en production sur plusieurs services isolés et sécurisés par des secrets Swarm.**
> *Un `docker-compose.yml` unique qui orchestre un site WordPress derrière un reverse proxy Nginx, une base de données MariaDB protégée par des secrets, une interface d'administration Portainer, et une application de chat façon Discord ("cord-chat") construite maison.*

---

## 🧐 Aperçu
![P-Docker](Asset/Img/banner.png)

Le projet **P-Docker** est un fichier `docker-compose.yml` conçu pour **Docker Swarm** (et non un simple `docker compose up` classique). Il déploie 6 services répartis sur un cluster :

| # | Service | Rôle |
|---|---------|------|
| 1 | `mariadb` | Base de données MySQL/MariaDB pour WordPress, isolée sur le réseau `backend` |
| 2 | `wordpress` | CMS WordPress (image `fpm-alpine`), connecté au `backend` et au `frontend` |
| 3 | `nginx` | Reverse proxy qui sert WordPress sur le port `80` |
| 4 | `agent` | Agent Portainer déployé en mode `global` sur chaque nœud du swarm |
| 5 | `portainer` | Interface d'administration Portainer, exposée sur le port `666` |
| 6 | `chat` | Clone de Discord fait maison (`discord-clone`), exposé sur le port `555` |

Tout est pensé pour la **production** : mots de passe injectés via **Docker Secrets** (jamais en clair), réseaux **overlay** séparés (`frontend` / `backend` / `agent_network`), et contraintes de placement pour garder les volumes locaux sur le bon nœud manager.

## ✨ Fonctionnalités
- ✅ **Stack WordPress complète** : WordPress + MariaDB + Nginx, prêts à l'emploi derrière un reverse proxy.
- ✅ **Sécurité par Docker Secrets** : les mots de passe de la base de données (`db_password`, `db_root_password`) ne sont jamais écrits en clair dans le compose, ils sont injectés via `/run/secrets/`.
- ✅ **Isolation réseau** : deux réseaux overlay distincts (`frontend` et `backend`) empêchent la base de données d'être exposée sur Internet.
- ✅ **Supervision avec Portainer** : un serveur Portainer + un agent déployé en mode `global` (un par nœud) pour piloter tout le cluster depuis une seule UI, sur le port `666`.
- ✅ **Chat maison ("cord-chat")** : un clone de Discord buildé depuis le dossier `discord-clone/`, exposé sur le port `555`, avec persistance de l'historique des messages via un volume dédié (`chat_data`).
- ✅ **Haute disponibilité partielle** : WordPress et Nginx tournent avec plusieurs réplicas (`replicas: 2`), avec `restart_policy: on-failure` sur tous les services.
- ✅ **Placement contrôlé** : les services qui dépendent de volumes locaux sont contraints (`node.id`) pour rester sur le nœud manager qui possède les données.

## 🛠 Tech Stack
| Technologie | Usage |
| :--- | :--- |
| ![Docker Swarm](https://img.shields.io/badge/Docker_Swarm-2496ED?style=flat-square&logo=docker&logoColor=white) | Orchestration multi-nœuds des services |
| ![WordPress](https://img.shields.io/badge/WordPress-6.8.2-21759B?style=flat-square&logo=wordpress&logoColor=white) | CMS principal (image `fpm-alpine`) |
| ![MariaDB](https://img.shields.io/badge/MariaDB-LTS-003545?style=flat-square&logo=mariadb&logoColor=white) | Base de données de WordPress |
| ![Nginx](https://img.shields.io/badge/Nginx-1.28.0_alpine-009639?style=flat-square&logo=nginx&logoColor=white) | Reverse proxy / serveur de fichiers statiques |
| ![Portainer](https://img.shields.io/badge/Portainer-CE-13BEF9?style=flat-square&logo=portainer&logoColor=white) | Administration graphique du cluster Swarm |
| ![Node.js](https://img.shields.io/badge/Node.js-Chat-339933?style=flat-square&logo=node.js&logoColor=white) | Application de chat maison (`discord-clone`) |

## 🚀 Installation & Lancement

> ⚠️ **Ce projet est fait pour Docker Swarm, pas pour un `docker compose up` classique.** Suis bien les étapes ci-dessous dans l'ordre, sinon le déploiement échouera (secrets/réseaux "external" manquants).

### 1. Cloner le projet
```bash
git clone https://github.com/BlackAngelTVdev/P-Docker.git
cd P-Docker
```

### 2. Initialiser Docker Swarm (si ce n'est pas déjà fait)
```bash
docker swarm init
```
> Si tu déploies sur plusieurs machines, récupère le token de jointure affiché et exécute `docker swarm join ...` sur chaque worker.

### 3. Créer les réseaux overlay externes
Le compose référence des réseaux `frontend` et `backend` en `external: true`, il faut donc les créer **avant** le déploiement :
```bash
docker network create --driver overlay frontend
docker network create --driver overlay backend
```

### 4. Créer les secrets Docker Swarm
Les mots de passe de la base de données ne sont **jamais** en clair dans le fichier. Crée-les manuellement :
```bash
# Mot de passe de l'utilisateur MySQL "wp_user"
printf "TonMotDePasseSecurise" | docker secret create db_password -

# Mot de passe root de MariaDB
printf "TonMotDePasseRootSecurise" | docker secret create db_root_password -
```
> 💡 Utilise des mots de passe forts (générés avec `openssl rand -base64 24` par exemple), et ne les commit jamais dans un fichier `.env` versionné.

### 5. Adapter les contraintes de placement à ton cluster
Le fichier `docker-compose.yml` contient des contraintes du type :
```yaml
placement:
  constraints:
    - node.id == m0lv4bzwgvei55titkfxjx3fl
```
Cet identifiant correspond au nœud manager d'origine du projet. Remplace-le par l'ID de **ton propre nœud manager** :
```bash
docker node ls
```
Copie l'ID du nœud manager affiché et remplace toutes les occurrences de `m0lv4bzwgvei55titkfxjx3fl` dans `docker-compose.yml` par cet ID.

### 6. Builder l'image du chat (obligatoire avant le déploiement)
`docker stack deploy` **ignore** la directive `build:`, il faut donc construire l'image du chat manuellement en amont :
```bash
docker compose build chat
```
Cela va builder l'image `cord-chat:1.0.0` à partir du dossier `discord-clone/`.

### 7. Vérifier la configuration Nginx
Le service `nginx` charge sa configuration via un `config` Docker Swarm, à partir de `./nginx/default.conf`. Assure-toi que ce fichier existe et pointe bien vers les bons services internes (`wordpress:9000`, etc.) avant le déploiement.

### 8. Déployer la stack
```bash
docker stack deploy -c docker-compose.yml p-docker
```

### 9. Vérifier que tout tourne
```bash
docker stack services p-docker
docker stack ps p-docker
```
Tous les services doivent afficher l'état `Running` (les réplicas peuvent prendre quelques secondes à démarrer).

### 10. Accéder aux services
| Service | URL |
| :--- | :--- |
| Site WordPress | `http://<IP-DU-NŒUD>:80` |
| Interface Portainer | `http://<IP-DU-NŒUD>:666` |
| Interface HTTPS Portainer | `https://<IP-DU-NŒUD>:9443` |
| Chat (cord-chat) | `http://<IP-DU-NŒUD>:555` |

À la première connexion à WordPress, suis l'assistant d'installation classique (langue, titre du site, identifiants admin). Pour Portainer, la première connexion te demandera de créer le compte administrateur.

## 📖 Utilisation

Une fois la stack déployée, tu peux la gérer comme n'importe quelle stack Docker Swarm :

```bash
# Voir les logs d'un service en direct
docker service logs -f p-docker_wordpress

# Scaler un service (ex: passer Nginx à 3 réplicas)
docker service scale p-docker_nginx=3

# Mettre à jour un service après un changement d'image
docker service update --image wordpress:6.8.3-fpm-alpine p-docker_wordpress

# Supprimer entièrement la stack
docker stack rm p-docker
```

Pour rebuild et redéployer le chat après une modification du code source dans `discord-clone/` :
```bash
docker compose build chat
docker service update --force p-docker_chat
```

## 🤝 Contribution
1. Forkez le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Commit (`git commit -m 'Add some AmazingFeature'`)
4. Push (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 👤 Auteur

**BlackAngelTVdev**
![Follow](https://img.shields.io/github/followers/BlackAngelTVdev?label=Follow%20Me&style=social)

---
## 📄 Licence

Ce projet est sous licence :
![GitHub License](https://img.shields.io/github/license/BlackAngelTVdev/P-Docker?style=flat-square&color=blue)

### 🧑‍💻 Contributors

Merci à toutes les personnes qui contribuent au projet.

[![Contributors](https://contrib.rocks/image?repo=BlackAngelTVdev/P-Docker)](https://github.com/BlackAngelTVdev/P-Docker/graphs/contributors)
