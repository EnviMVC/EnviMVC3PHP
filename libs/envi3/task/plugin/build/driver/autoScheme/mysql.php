<?php

// DBに接続して、自動的にスキーマ情報を取得する
if ($auto_schema) {
    $schema['schema'] = array();
    if (!isset($EnviDBInstance)) {
        $EnviDBInstance = new EnviDBInstance($database_yaml);
    }
    $dbi = $EnviDBInstance->getInstance($instance_name);
    $schema_arr = $dbi->getAll('desc '.$table_name);
    $index_schema_arr = $dbi->getAll('SHOW INDEX FROM '.$table_name);

    foreach ($schema_arr as $k => $arr) {
        $schema['schema'][$arr['Field']]['type']    = $arr['Type'];
        switch ($arr['Default']) {
            case 'CURRENT_TIMESTAMP':
                $arr['Default'] = NULL;
                break;
            default:
                break;
        }
        $schema['schema'][$arr['Field']]['default'] = $arr['Default'];

        foreach ($index_schema_arr as $index_arr) {
            if ($index_arr['Column_name'] === $arr['Field']) {
                if ($index_arr['Non_unique'] == 0 && strtolower($index_arr['Key_name']) === 'primary') {
                    $schema['schema'][$arr['Field']]['primary'] = $index_arr['Key_name'];
                } elseif ($index_arr['Non_unique'] == 0) {
                    $schema['schema'][$arr['Field']]['unique'][] = $index_arr['Key_name'];
                } else {
                    $schema['schema'][$arr['Field']]['index'][] = $index_arr['Key_name'];
                }
            }
        }
        if (strtolower($arr['Null']) === 'no') {
            $schema['schema'][$arr['Field']]['not_null'] = true;
        }
        if (strtolower($arr['Extra']) === 'auto_increment') {
            $schema['schema'][$arr['Field']]['auto_increment'] = true;
        }

    }
}
