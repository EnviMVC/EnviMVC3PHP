定義済み定数
===========================================================
<?php
$is_flag = false;
foreach ($class_list as $class_name => $class_item) {
    $arr = $class_item['token']->getConstantList();
    if (!(is_array($arr) && count($arr))) {
        continue;
    }
    if (!$is_flag) {
        echo "以下の定数が定義されています。\n\n";
        $is_flag = true;
    }
    foreach ($arr as $token) {
        $subject = '説明はありません';
        if ($token->getDocBlockToken() instanceof EnviParserToken) {
            $subject = $token->getDocBlockToken()->getDocBlockSubject();
        }
        echo $class_name,"::",$token->getName(),"
:   ",$subject,"\n\n";
    }
}

if (!$is_flag) {
    echo "定義済みの定数はありません。\n\n";
    $is_flag = true;
}
?>
