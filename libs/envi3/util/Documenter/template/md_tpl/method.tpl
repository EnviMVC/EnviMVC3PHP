<?=$method_name?>

===========================================================
<?php
$doc_array = $token->getDocBlockToken()->getDocBlockArray();
$class_doc_array = $class_token->getDocBlockToken()->getDocBlockArray();
$file_doc_array = $file_doc_token->getDocBlockArray();

$since = '3.0.0';
if (isset($doc_array['since'][0][0])) {
    mb_ereg('([0-9]*\.[0-9]*\.[0-9]*)', join(' ', $doc_array['since'][0]), $since);
    $since = $since[1];
} elseif (isset($class_doc_array['since'][0][0])) {
    mb_ereg('([0-9]*\.[0-9]*\.[0-9]*)', join(' ', $class_doc_array['since'][0]), $since);
    $since = $since[1];
} elseif (isset($file_doc_array['since'][0][0])) {
    mb_ereg('([0-9]*\.[0-9]*\.[0-9]*)', join(' ', $file_doc_array['since'][0]), $since);
    $since = $since[1];
}

?>
(<?=$since?>～)


説明
------------------------------------------------------------

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_method}<?php

$return_text = '';
if (isset($doc_array['return'][0][0])) {
    $return_text = $doc_array['return'][0][0];
}

$keywords = $token->getKeywords();
if (is_array($keywords)) {
    $keywords = join(' ', $keywords);
}


$arguments = $token->getArguments();

$i = 0;
foreach ($arguments as $k => $v) {
    $arguments[$k] = '';
    if ($v['optional'] !== NULL) {
        $arguments[$k] .= '[ ';
    }
    if ($v['type_hint'] !== NULL) {
        $arguments[$k] .= $v['type_hint'].' '.$k;
    } elseif (isset($doc_array['param'][$i][0], $doc_array['param'][$i][1]) &&
        $doc_array['param'][$i][1] === $k
    ) {
        $arguments[$k] .= $doc_array['param'][$i][0].' '.$k;
    } else {
        echo $method->getMethodName().' error';
        var_dump($doc_array['param'][$i]);
        var_dump($k);
        die();
    }
    if ($v['optional'] !== NULL) {
        $arguments[$k] .= ' = '.$v['optional'].']';
    }
    ++$i;
}
$argument = ' ';
if (is_array($arguments)) {
    $argument = join(', ', $arguments);
}
?>

<?=$keywords?> <?=$return_text?> <?=$token->getName()?> (<?=$argument?>)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?=$token->getDocBlockToken()->getDocBlockSubject()?>


<?php
if (isset($doc_array['deprecated'])) {
if (!isset($doc_array['deprecated'][0][0])) {
    $doc_array['deprecated'][0][0] = '今後削除される予定のメソッドです。使用されるべきではありません。';
} else {
    $doc_array['deprecated'][0][0] = join("\n\n", $doc_array['deprecated'][0]);
}
?>

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-info}
__※__ 非推奨 <?=$doc_array['deprecated'][0][0]?>

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

<?php
}
?>
<?php
    $dock_block_all = $token->getDocBlockToken()->getDocBlockDetail();
    $example        = NULL;
    $example_result = NULL;
    $list = explode('{@example}', $dock_block_all);
    if (count($list) === 2) {
        echo $list[0];
        $list = explode('{/@example}', $list[1]);
        $example = $list[0];
        if (count($list) === 2) {
            $list = explode('{@example_result}', $list[1]);
            if (count($list) === 2) {
                $list = explode('{@example_result}', $list[1]);
                $example_result = $list[0];
            }
        }
    } else {
        echo $dock_block_all;
    }
?>



パラメータ
------------------------------------------------------------

<?php
if (isset($doc_array['param'])) {
foreach ($doc_array['param'] as $val) {
    $character = array_shift($val);
    $param = array_shift($val);
    $description = join(' ', $val);
?>
<?=$param?>

: <?=$description?>


<?php
}
}
?>



返り値
-------------------------------------------------------------
<?php
    if (isset($doc_array['return'][0][0]) && $doc_array['return'][0][0] === 'void') {
        ?>このメソッドは返り値を返しません<?php
    } elseif (!isset($doc_array['return'][0][1])) {
        ?>　　　<?php
    } else {
        ?><?=$doc_array['return'][0][1]?><?php
    }
?>

<?php if ($example) { ?>
例
-------------------------------------------------------------

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
<?=trim($example)?>

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?php if ($example_result) { ?>
上の例の出力は以下となります。

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?=$example_result?>

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
<?php }} ?>

参考
-------------------------------------------------------------
<?php
if (isset($doc_array['see'])) {
foreach ($doc_array['see'] as $see) {
    list($see[0]) = explode('(', $see[0]);
    if (!isset($method_list[$see[0]])) {
        continue;
    }
    mb_ereg('document/man/(.*).md$', $method_list[$see[0]]['man_path'], $man_path);

    echo "* [",$see[0],"](/c/man/v3/reference/",$man_path[1],") — ",$method_list[$see[0]]['token']->getDocBlockToken()->getDocBlockSubject(),"\n";

}


}
?>

