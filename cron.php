<?php
require 'request.php';
$params_delete=[
    ['key'=>'used','value'=>'1','symb'=>'=']
];
$db->delete('timetable',$params_delete);
echo date("H:i:s").' - '.'Очистили базу данных от использованных событий'.PHP_EOL;