

#### 1. bfmtv1.txt et bfmtv2.txt sont des assemblages sélectif :

- de listes de titres et d'éléments de titres générées par ChatGPT


#### 2. brands.txt est un assemblage très sélectif :

- de listes de marques générées par ChatGPT
- principalement autour de l’électronique, l’électroménager, l'automobile, l'alimentation, la restauration rapide et la grande distribution


#### 3. feels.txt est un assemblage sélectif de différentes sources :

- http://expertise.uriopss-npdc.asso.fr/resources/npca/pdfs/2018/6_Juin//Liste_des_sentiments_FAURE.pdf
- https://www.zebrezen.fr/liste-des-emotions/
- https://apprendreaeduquer.fr/tableau-des-nuances-des-emotions-un-outil-pour-developper-le-vocabulaire-des-enfants-autour-des-emotions/


#### 4. modals.txt est un assemblage sélectif :

- de posts roses issus des topics :  bli, cha, cim, cov, cul, dem, eur, exg, foo, for, gif, gil, gsn, hdl, img, inf, ins, jop, lfi, lib, lrm, lux, med, met, moy, nat, noe, pho, que, rep, scr, soc, sor, sub, ter, ukr, upr, usp, vrt et zem ( voir https://roger21.github.io/stats/ )
- globalement (mais pas entièrement) décontextualisés (thèmes et pseudals impliqués principalement)
- modifiés pour s'adapter à la forme du générateur
- légèrement corrigés (syntaxe, grammaire, ponctuation, typo, ...)


#### 5. names.txt est un assemblage sélectif de différentes sources :

- les mots `:M1` du fichier `French.lex` de Grammalecte : <http://grammalecte.net:8080/file?name=lexicons/French.lex&ci=tip>
- https://fr.wikimini.org/wiki/Liste_des_pr%C3%A9noms_fran%C3%A7ais
- https://fr.wikipedia.org/wiki/Liste_de_pr%C3%A9noms_en_fran%C3%A7ais
- https://www.herault.gouv.fr/content/download/39729/260550/file/Pr%C3%A9noms%20f%C3%A9minin%20pdf.pdf
- https://www.oise.gouv.fr/contenu/telechargement/68118/413742/file/liste_alphabetique_des_prenoms_masculins_acceptes_pour_une_demande_de_francisation.pdf
- https://www.prefecturedepolice.interieur.gouv.fr/sites/default/files/Documents/liste_prenoms.pdf


#### 6. pseudals.txt est un top 1000 des profils en nombre de posts au jeudi 11 décembre 2025

- ayant un avatar ;
- existant et ayant un avatar au lundi 13 juillet 2020 ;
- ayant posté depuis moins d'un mois au jeudi 11 décembre 2025 :

```
WITH old AS (SELECT pseudal FROM old.p WHERE avatarurl IS NOT NULL)
SELECT new.pseudal FROM new.p new, old WHERE
avatarurl IS NOT NULL AND new.pseudal = old.pseudal AND
lastpostdate > date - interval '1 month'
ORDER BY nbposts DESC LIMIT 1000
```

WITH old AS (SELECT pseudal FROM old.p WHERE avatarurl IS NOT NULL)
SELECT rank() over (order by nbposts desc, pseudal), new.pseudal, quote, nbposts, date - lastpostdate, profileurl, postsurl
FROM new.p new NATURAL JOIN old
WHERE avatarurl IS NOT NULL AND btrim(quote) != '' AND date - lastpostdate > interval '1 year' AND nbposts > 10000
ORDER BY nbposts desc



#### 7. quotes.txt contient les signatures des profils au jeudi 11 décembre 2025
- ayant posté depuis moins d'un an au jeudi 11 décembre 2025 ;
- nettoyées pour ne garder que des celles qui peuvent constituer une citation (en gros) ;
- auxquelles je comprends quelque chose (en gros) ;
- légèrement corrigées (syntaxe, grammaire, ponctuation, typo, ...) ;
- découpées en plusieurs citations quand il y en a plusieurs ;
- en excluant les signatures descriptives (en gros) et les dialogues (en gros aussi) :

```
SELECT btrim(signature) FROM p WHERE
btrim(signature) != '' AND
lastpostdate > date - interval '1 year'
```


#### 8. words.txt est constitué de :

- les mots `:N` du fichier `French.lex` de Grammalecte : http://grammalecte.net:8080/file?name=lexicons/French.lex&ci=tip
- qui sont également présents dans le fichier `dictDecl.txt` de Grammalecte : http://grammalecte.net:8080/file?name=gc_lang/fr/data/dictDecl.txt&ci=tip
- et les mots `:J` `:B` et `:ÉW` du fichier `French.lex` de Grammalecte : http://grammalecte.net:8080/file?name=lexicons/French.lex&ci=tip


