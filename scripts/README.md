#### les scripts pour robot21

met à jour [la liste des smileys](https://github.com/roger21/toyo/blob/main/generateurs/_api/smileys.txt) pour [l'api de recherche](https://github.com/roger21/toyo/blob/main/generateurs/_api/get_by_name.php) et [les générateurs](https://generateurs.super-h.fr/) et post les changements sur le forum [ici](https://forum.hardware.fr/forum2.php?post=54724&cat=13&word=&spseudo=robot21&filter=1) ou [là](https://forum.hardware.fr/forum2.php?post=148758&cat=10&word=&spseudo=robot21&filter=1)

la version [workflow](https://github.com/roger21/toyo/blob/main/scripts/update_smileys_workflow.php) ([yaml](https://github.com/roger21/toyo/blob/main/.github/workflows/update_smileys_workflow.yaml)) est désactivée, la version [local](https://github.com/roger21/toyo/blob/main/scripts/post_smiley_update_local.php) est lancée le samedi à 10h

la fonction de postage est dans [functions.php](https://github.com/roger21/toyo/blob/main/scripts/functions.php), les cookies ont la forme `"md_user=pseudal; md_passs=md5"`, voir aussi le [topic idoine](https://forum.hardware.fr/hfr/Programmation/sujet_146781_1.htm)