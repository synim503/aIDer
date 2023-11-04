<?php

require 'request.php';

while (true){
    $date = date("Y-m-d H:i:s");

    $params_select=[
        ['key'=>'unix','value'=>$date,'symb'=>'<'],
        ['key'=>'used','value'=>'False','symb'=>'=']
    ];

    $list_work = $db->select('timetable','id,user_id,type, unix, used',$params_select);

    if(empty($list_work)){
        echo date("H:i:s").' - '.'Работы нет';
        echo PHP_EOL;
        sleep(5);
        continue;
    }

    for($i=0;$i<count($list_work);$i++){

        if($list_work[$i]['type']=='Like'){
            $make_like = make_like($list_work[$i]['user_id'],get_rand_profile('en')['auth_token']);

            if ($make_like['status']='200'){
                echo date("H:i:s").' - '."Поставили лайк {$list_work[$i]['user_id']}".PHP_EOL;
                $params_update = [
                    ['key'=>'id','value'=>$list_work[$i]['id'],'symb'=>'=']
                ];
                $db->update('timetable',$params_update,['used'=>'1']);
            }else echo "Неудалось сделать визит. Ошибка: ".$make_like['error'];


        }

        elseif($list_work[$i]['type']=='Visit'){
            $make_visit = make_visit($list_work[$i]['user_id'],get_rand_profile('en')['auth_token']);
            if ($make_visit['status']='200') {
                echo date("H:i:s") . ' - ' . "Сделали визит {$list_work[$i]['user_id']}" . PHP_EOL;
                $params_update = [
                    ['key' => 'id', 'value' => $list_work[$i]['id'], 'symb' => '=']
                ];
                $db->update('timetable', $params_update, ['used' => '1']);
            }else echo "Неудалось сделать визит. Ошибка: ".$make_visit['error'];
        };

        //sleep(1);
    }
}