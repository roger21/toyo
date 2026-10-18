#### les alertes qualitaÿ https://aq.super-h.fr/rss.php

les erreurs sont loggées dans le fichier `errors/errors.log`

(le serveur web doit avoir les droits d'écriture sur le répertoire `errors`)

le fichier `config/alerteQualitay.config.php` doit être configuré :
- `URL_BASE` : l'url de base du serveur, utilisée par le rss
- `MAIL_ADDRESS` : l'adresse mail où envoyer les notifications
- `MAIL_LINKS` : du contenu complémentaire pour les notifications
- les paramètres `AQ_DB_*` : pour la connexion à la base de données

le dernier dump de la base de données est dans `sql/db.sql` (19 avril 2026)