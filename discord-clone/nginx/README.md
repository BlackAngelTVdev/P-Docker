# nginx : Cord (chat) + GitLab derrière le port 80

Si seuls les ports 80/443 sont accessibles depuis l'extérieur (pare-feu,
routeur…), ni `IP:555` (chat) ni le port du conteneur GitLab ne répondent.
La solution : faire passer les deux applications par nginx sur le port 80.

- **Cord** → `http://<IP-du-serveur>/`
- **GitLab** → `http://<IP-du-serveur>/gitlab`

## 1. Vérifier ce qui tourne sur le serveur

```bash
# Le chat répond-il sur 555 ? (doit afficher un header HTTP)
curl -I http://127.0.0.1:555
# Si « connection refused » : le conteneur n'est pas lancé.
cd <ce-dépôt> && docker compose up -d --build

# Port réel du conteneur GitLab (colonne PORTS) :
docker ps
# Exemple : 0.0.0.0:8080->80/tcp  →  port hôte 8080
```

Reportez le port GitLab dans `upstream gitlab_backend` de `cord.conf`
(ou `subdomain.conf`).

## 2. Configurer GitLab une seule fois (sous-chemin `/gitlab`)

Dans `gitlab.rb` du conteneur GitLab (Omnibus), remplacez l'`external_url` :

```ruby
external_url 'http://<IP-du-serveur>/gitlab'
```

puis reconfigurer GitLab avec `gitlab-ctl reconfigure` (ou recréer le
conteneur). GitLab calcule lui-même `relative_url_root='/gitlab'` et génère
tous ses liens avec ce préfixe.

> Avec un nom de domaine ? Utilisez plutôt `nginx/subdomain.conf` et
> `external_url 'http://gitlab.votredomaine.fr'` — pas de sous-chemin.

## 3. Installer le site nginx

```bash
sudo cp nginx/cord.conf /etc/nginx/sites-available/cord
sudo ln -s /etc/nginx/sites-available/cord /etc/nginx/sites-enabled/cord
sudo nginx -t        # vérifie la syntaxe
sudo systemctl reload nginx
```

Si `nginx -t` signale un conflit de `server_name`/`listen`, c'est qu'un autre
site nginx occupe déjà le port 80 — désactivez-le ou fusionnez les deux
fichiers de `sites-enabled`.

## 4. Ouvrir le port 80

```bash
sudo ufw allow 80/tcp        # si ufw est actif
# + éventuellement le port 80 dans l'interface du routeur / de l'hébergeur
```

## 5. Accéder

- Chat : `http://<IP-du-serveur>/`
- GitLab : `http://<IP-du-serveur>/gitlab`

> Le micro (salons vocaux) reste bloqué par le navigateur tant qu'on est en
> HTTP pur hors `localhost` — il faudra passer en HTTPS (certbot/Let's Encrypt)
> pour la voix depuis d'autres machines.