<?php

// DBに接続して、自動的にスキーマ情報を取得する
if ($auto_schema) {
    $schema['schema'] = array();
    if (!isset($EnviDBInstance)) {
        $EnviDBInstance = new EnviDBInstance($database_yaml);
    }
    $dbi = $EnviDBInstance->getInstance($instance_name);
    $schema_arr = ($dbi->getAll('SELECT * FROM all_tab_columns WHERE TABLE_NAME = ? ORDER BY COLUMN_ID', array($table_name)));
    $index_constraints_arr = ($dbi->getAll('SELECT * FROM user_constraints  WHERE TABLE_NAME = ? ', array($table_name)));
    $index_schema_arr      = ($dbi->getAll('SELECT * FROM user_indexes  WHERE TABLE_NAME = ? ', array($table_name)));
    $index_ind_columns_arr = ($dbi->getAll('SELECT * FROM user_ind_columns  WHERE TABLE_NAME = ? ', array($table_name)));


    foreach ($schema_arr as $k => $arr) {
        $schema['schema'][$arr['COLUMN_NAME']]['type']    = $arr['DATA_TYPE'];
        switch ($arr['DATA_DEFAULT']) {
            case 'CURRENT_TIMESTAMP':
                $arr['DATA_DEFAULT'] = NULL;
                break;
            default:
                break;
        }
        if ($arr['DATA_SCALE'] === NULL) {
            $schema['schema'][$arr['COLUMN_NAME']]['type'] .= '('.$arr['DATA_LENGTH'].')';
        } elseif ($arr['DATA_PRECISION'] === NULL) {
            $schema['schema'][$arr['COLUMN_NAME']]['type'] .= '('.$arr['DATA_SCALE'].')';
        } else {
            $schema['schema'][$arr['COLUMN_NAME']]['type'] .= '('.$arr['DATA_PRECISION'].','.$arr['DATA_SCALE'].')';
        }

        $schema['schema'][$arr['COLUMN_NAME']]['default'] = $arr['DATA_DEFAULT'];

        foreach ($index_ind_columns_arr as $index_arr) {
            if ($index_arr['COLUMN_NAME'] === $arr['COLUMN_NAME']) {
                foreach ($index_constraints_arr as $contains_arr) {
                    if ($contains_arr['INDEX_NAME'] === $index_arr['INDEX_NAME'] && $contains_arr['CONSTRAINT_TYPE'] === 'P'){
                        $schema['schema'][$arr['COLUMN_NAME']]['primary']  = $index_arr['INDEX_NAME'];
                    }
                }
                foreach ($index_schema_arr as $contains_arr) {
                    if ($arr['COLUMN_NAME'] ===  $index_arr['COLUMN_NAME'] && $contains_arr['INDEX_NAME'] === $index_arr['INDEX_NAME'])
                    if ($contains_arr['UNIQUENESS'] === 'UNIQUE') {
                        $schema['schema'][$arr['COLUMN_NAME']]['unique'][] = $index_arr['INDEX_NAME'];
                    } else {
                        $schema['schema'][$arr['COLUMN_NAME']]['index'][]  = $index_arr['INDEX_NAME'];
                    }
                }
            }
        }

        $schema['schema'][$arr['COLUMN_NAME']]['not_null'] = false;
        foreach ($index_constraints_arr as $contains_arr) {
            if ($contains_arr['SEARCH_CONDITION'] === '"'.$arr['COLUMN_NAME'].'" IS NOT NULL') {
                $schema['schema'][$arr['COLUMN_NAME']]['not_null'] = true;
            }
        }

    }
}