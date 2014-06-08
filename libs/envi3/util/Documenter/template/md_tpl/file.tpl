<?=$file_name?>

================================================================
* [導入](./<?=$file_name?>/intro)
* [インストール/設定](./<?=$file_name?>/setup)
* [定義済み定数](./<?=$file_name?>/constants)
<?php foreach ($class_list as $class_name => $class_item) { ?>
* [<?=$class_name?>](./<?=$file_name?>/<?=$class_name?>) — <?=$class_item['token']->getDocBlockToken()->getDocBlockSubject()?>

<?php foreach ($class_item['methods'] as $method_name => $method) { ?>
  * [<?=$method->getMethodName()?>](./<?=$file_name?>/<?=$class_name?>/<?=$method->getName()?>) — <?=$method->getDocBlockToken()->getDocBlockSubject()?>

<?php } ?>
<?php } ?>