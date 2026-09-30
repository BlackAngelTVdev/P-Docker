# 📓 Journal de travail — Projet P-Docker

Suivi hebdomadaire du temps passé sur le projet (déploiement Docker Swarm : WordPress, MariaDB, Nginx, Portainer, Chat).

---

## Mercredi 26 août 2026 — 3h

**Objectif :** Mise en place de la base du projet.

- Initialisation du dépôt Git et structure des dossiers (`nginx/`, `wp-theme/`, `discord-clone/`).
- Rédaction du premier jet du `docker-compose.yml` (services `mariadb` et `wordpress`).
- Configuration des réseaux overlay `frontend` et `backend`.
- Tests de connexion entre WordPress et MariaDB en local.

---

## Mercredi 2 septembre 2026 — 3h

**Objectif :** Sécurisation et ajout du reverse proxy.

- Mise en place des Docker Secrets (`db_password`, `db_root_password`) pour éviter les mots de passe en clair.
- Ajout et configuration du service `nginx` en reverse proxy devant WordPress.
- Écriture du fichier `nginx/default.conf`.
- Correction d'un souci de permissions sur le volume `wp_data`.

---

## Mercredi 9 septembre 2026 — 3h

**Objectif :** Supervision, chat maison et documentation.

- Ajout des services `portainer` et `agent` pour la supervision du cluster Swarm.
- Intégration du service `chat` (clone de Discord fait maison) avec build depuis `discord-clone/`.
- Configuration des contraintes de placement (`node.id`) pour garder les volumes locaux sur le bon nœud.
- Rédaction du README.md complet du projet (présentation, installation, utilisation).

---

## Mercredi 30 septembre 2026 

**Objectif** 80%

- mise a jour du kanban
- mise a jour du JNR
- Verification du bon fonctionnement 
## ⏱️ Total cumulé : 9h
