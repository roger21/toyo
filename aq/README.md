#### les aq de toyo

la dernière base est dans sql/db.sql (maj 19 avril 2026)

les erreurs sont loggées dans errors/errors.log (le repertoire errors doit autoriser l'écriture pour le serveur web)

dans le fichier config/alerteQualitay.config.php il faut mettre à jour URL_BASE (l'url de base du serveur pour le rss), MAIL_ADDRESS (l'adresse mail pour envoyer des mails d'alerte et d'information), MAIL_LINKS (pour rajouter des liens ou du contenu dans les mails) et les paramètres AQ_DB_* pour la connexion à la base de données
