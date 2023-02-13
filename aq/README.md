#### les aq de toyo

la dernière base est dans sql/db.sql

les erreurs sont loggées dans errors/errors.log (le repertoire errors doit autoriser l'écriture pour le serveur web)

dans le fichier config/alerteQualitay.config.php il faut mettre à jour BASE_URL, MAIL_ADDRESS (pour les mails d'alerte et d'info) et MAIL_LINKS (pour rajouter des liens ou du contenu dans les mails d'infos)

et dans le fichier dao/daoAlerteQualitayMySql.class.php il faut rensseigner les paramètres de connection à la base (host, user, passwd et db)