-- pas de rapporteur sans aq (ne doit pas retourner de ligne)

select * from rapporteur where alerte_qualitay_id not in (select id from alerte_qualitay) order by id

-- pas d'aq sans rapporteur (ne doit pas retourner de ligne)

select * from alerte_qualitay where id not in (select alerte_qualitay_id from rapporteur) order by id

-- unicité des initiateur par url (ne doivent pas retourner de ligne)

select count(*) nb, post_url from rapporteur where initiateur group by post_url having nb > 1

select * from rapporteur where post_url in (select post_url from (select count(*) nb, post_url from rapporteur where initiateur group by post_url having nb > 1) t) and initiateur order by post_url, id

select * from alerte_qualitay where id in (select alerte_qualitay_id from (select * from rapporteur where post_url in (select post_url from (select count(*) nb, post_url from rapporteur where initiateur group by post_url having nb > 1) t) and initiateur order by post_url, id) t) order by id

