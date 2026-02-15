### _[bfmtv1.txt](bfmtv1.txt)_ et _[bfmtv2.txt](bfmtv2.txt)_ sont des assemblages sélectifs :
- de listes de titres et d'éléments de titres générés par ChatGPT

### _[brands.txt](brands.txt)_ est un assemblage très sélectif :
- de listes de marques générées par ChatGPT
- principalement autour de l’électronique, l’électroménager, l'automobile, l'alimentation, la restauration rapide et la grande distribution

### _[feels.txt](feels.txt)_ est un assemblage sélectif de différentes sources :
- <http://expertise.uriopss-npdc.asso.fr/resources/npca/pdfs/2018/6_Juin//Liste_des_sentiments_FAURE.pdf>
- <https://www.zebrezen.fr/liste-des-emotions/>
- <https://apprendreaeduquer.fr/tableau-des-nuances-des-emotions-un-outil-pour-developper-le-vocabulaire-des-enfants-autour-des-emotions/>

### _[modals.txt](modals.txt)_ est un assemblage sélectif :
- de posts roses issus des topics :  bli, cha, cim, cov, cul, dem, eur, exg, foo, for, gif, gil, gsn, hdl, img, inf, ins, jop, lfi, lib, lrm, lux, med, met, moy, nat, noe, pho, que, rep, scr, soc, sor, sub, ter, ukr, upr, usp, vrt et zem (voir <https://roger21.github.io/stats/>)
- globalement (mais pas entièrement) décontextualisés (thèmes et pseudals impliqués principalement)
- modifiés pour s'adapter à la forme du générateur
- légèrement corrigés (syntaxe, grammaire, ponctuation, typo, ...)

### _[names.txt](names.txt)_ est un assemblage sélectif de différentes sources :
- les mots `:M1` du fichier `French.lex` de Grammalecte : <http://grammalecte.net:8080/file?name=lexicons/French.lex&ci=tip>
- <https://fr.wikimini.org/wiki/Liste_des_pr%C3%A9noms_fran%C3%A7ais>
- <https://fr.wikipedia.org/wiki/Liste_de_pr%C3%A9noms_en_fran%C3%A7ais>
- <https://www.herault.gouv.fr/content/download/39729/260550/file/Pr%C3%A9noms%20f%C3%A9minin%20pdf.pdf>
- <https://www.oise.gouv.fr/contenu/telechargement/68118/413742/file/liste_alphabetique_des_prenoms_masculins_acceptes_pour_une_demande_de_francisation.pdf>
- <https://www.prefecturedepolice.interieur.gouv.fr/sites/default/files/Documents/liste_prenoms.pdf>

### _[pseudals.txt](pseudals.txt)_ contient les pseudos et les « citations personnelles associées aux pseudos » :
- des profils ayant un avatar, une « citation personnelle associée au pseudo » et au moins 10 000 posts au jeudi 11 décembre 2025
- existant et ayant un avatar, une « citation personnelle associée au pseudo » et au moins 10 000 posts au lundi 13 juillet 2020
- n'ayant **PAS** posté depuis plus d'un an au 11 décembre 2025 (en gros)
- et dont la « citation personnelle associée au pseudo » ne contient pas de caractère non géré par la police d'écriture du générateur
```sql
WITH old AS (
  SELECT
    pseudal
  FROM
    old.p
  WHERE
    avatarurl IS NOT NULL
    AND btrim(quote, U&'\000A\000B\000C\000D\0009\0020\0085\2028\2029') != ''
    AND nbposts > 10000
)
SELECT
  pseudal || ';' || btrim(quote, U&'\000A\000B\000C\000D\0009\0020\0085\2028\2029')
FROM
  new.p NATURAL JOIN old
WHERE
  avatarurl IS NOT NULL
  AND btrim(quote, U&'\000A\000B\000C\000D\0009\0020\0085\2028\2029') != ''
  AND nbposts > 10000
  AND date - lastpostdate > interval '1 year'
```

### _[quotes.txt](quotes.txt)_ contient les signatures des profils au jeudi 11 décembre 2025 :
- ayant posté depuis moins d'un an au jeudi 11 décembre 2025
- nettoyées pour ne garder que des celles qui peuvent constituer une citation (en gros)
- auxquelles je comprends quelque chose (en gros)
- légèrement corrigées (syntaxe, grammaire, ponctuation, typo, ...)
- découpées en plusieurs citations quand il y en a plusieurs
- en excluant les signatures descriptives (en gros) et les dialogues (en gros aussi)
```sql
SELECT
  btrim(signature, U&'\000A\000B\000C\000D\0009\0020\0085\2028\2029')
FROM
  p
WHERE
  btrim(signature, U&'\000A\000B\000C\000D\0009\0020\0085\2028\2029') != ''
  AND date - lastpostdate < interval '1 year'
```

### _[words.txt](words.txt)_ est constitué de :
- les mots `:N` du fichier `French.lex` de Grammalecte : <http://grammalecte.net:8080/file?name=lexicons/French.lex&ci=tip>
- qui sont également présents dans le fichier `dictDecl.txt` de Grammalecte : <http://grammalecte.net:8080/file?name=gc_lang/fr/data/dictDecl.txt&ci=tip>
- et les mots `:J` `:B` et `:ÉW` du fichier `French.lex` de Grammalecte : <http://grammalecte.net:8080/file?name=lexicons/French.lex&ci=tip>
