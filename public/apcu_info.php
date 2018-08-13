<?php


$apcu_cache_info = apcu_cache_info();

$cache_list = $apcu_cache_info['cache_list'];

usort($cache_list, function ($a, $b) {
    return strcmp($a['info'], $b['info']);
});


foreach ($cache_list as $info) {
    var_dump($info);
}