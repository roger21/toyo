words.txt est un assemblage sélectif de différentes sources :

- https://gitlab.com/gpernot/wfrench
- https://www.vd.ch/guide-typo3/les-principes-de-redaction/redaction-egalitaire/2000-noms-au-masculin-et-au-feminin
- https://www.lalanguefrancaise.com/dictionnaire/classe-grammaticale/nom-commun


names.txt est un assemblage sélectif de différentes sources :

- https://fr.wikimini.org/wiki/Liste_des_pr%C3%A9noms_fran%C3%A7ais
- https://fr.wikipedia.org/wiki/Liste_de_pr%C3%A9noms_en_fran%C3%A7ais
- https://www.herault.gouv.fr/content/download/39729/260550/file/Pr%C3%A9noms%20f%C3%A9minin%20pdf.pdf
- https://www.oise.gouv.fr/contenu/telechargement/68118/413742/file/liste_alphabetique_des_prenoms_masculins_acceptes_pour_une_demande_de_francisation.pdf
- https://www.prefecturedepolice.interieur.gouv.fr/sites/default/files/Documents/liste_prenoms.pdf


feels.txt est un assemblage sélectif de différentes sources :

- http://expertise.uriopss-npdc.asso.fr/resources/npca/pdfs/2018/6_Juin//Liste_des_sentiments_FAURE.pdf
- https://www.zebrezen.fr/liste-des-emotions/
- https://apprendreaeduquer.fr/tableau-des-nuances-des-emotions-un-outil-pour-developper-le-vocabulaire-des-enfants-autour-des-emotions/


pseudals.txt est un top 1000 des profils en nombre de posts au jeudi 11 décembre 2025
- ayant un avatar ;
- existant et ayant un avatar au lundi 13 juillet 2020 ;
- ayant posté depuis moins d'un mois au jeudi 11 décembre 2025 :

WITH old AS (SELECT pseudal FROM old.p WHERE avatarurl IS NOT NULL),
last AS (SELECT min(date) - interval '1 month' date FROM new.p)
SELECT new.pseudal FROM new.p new, old, last WHERE
avatarurl IS NOT NULL AND new.pseudal = old.pseudal AND lastpostdate > last.date
ORDER BY nbposts DESC LIMIT 1000


