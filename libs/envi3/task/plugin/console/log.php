<?php
if (!isset($argv[3])) {
    eecho('引数が足りません。envi console-log <log_dir> <app_key> (<system:console:query:included_files>) (<backtrace:performance:log_text:memory_get_usage>)');
    die;
}
umask(0);


$get = !isset($argv[4]) ? explode(':', 'system:console:query:included_files') : explode(':', $argv[4]);
$filter = !isset($argv[5]) ? false : explode(':', $argv[5]);
$dir = realpath($argv[2]).DIRECTORY_SEPARATOR;
$app_key = $argv[3];
$console_log = $dir.$app_key.'console.log';
$query_log = $dir.$app_key.'query.log';
$system_log = $dir.$app_key.'system.log';
$included_files_log = $dir.$app_key.'included_files.log';

$cmd = 'tail -f';
foreach ($get as $file) {
    if ($file === 'console') {
        @touch($console_log);
        $cmd .= " {$console_log}";
    }
    if ($file === 'query') {
        @touch($query_log);
        $cmd .= " {$query_log}";
    }
    if ($file === 'system') {
        @touch($system_log);
        $cmd .= " {$system_log}";
    }
    if ($file === 'included_files') {
        @touch($included_files_log);
        $cmd .= " {$included_files_log}";
    }
}
$f = popen($cmd, 'r');
while ($res = fgets($f)) {
    $res = trim($res);
    $mode = $get[0];
    if (strpos($res, '==>') === 0) {
        switch (true) {
            case strpos($res, $console_log):
                $mode = 'console';
                echo $res."\n";
                break;
            case strpos($res, $query_log):
                $mode = 'query';
                echo $res."\n";
                break;
            case strpos($res, $system_log):
                $mode = 'system';
                echo $res."\n";
                break;
            case strpos($res, $included_files_log):
                $mode = 'included_files';
                echo $res."\n";
                break;
            default:
                break;
        }
        continue;
    }
    $res = @json_decode($res, true);
    if (!$res) {
        var_dump($res);
        continue;
    }
    if ($filter && $mode !== 'included_files') {
        $tmp = array();
        foreach ($filter as $key) {
            if (!isset($res[$key])) {
                continue;
            }
            $tmp[$key] = $res[$key];
        }
        $res = $tmp;
    }
    var_dump($res);

}
