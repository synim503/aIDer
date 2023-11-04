<?php
require 'request.php';
$сount_check_next_10=0;

while (true) {
    $lastid = $db->select('lastid', 'id')[0]['id'];
    $lastid++;
    $profile = get_rand_profile('ru');
    $auth_token=$profile['auth_token'];
    $load_user_date = get_laod_user_date(($lastid),$auth_token);
    $date = date("Y-m-d H:i:s");
    //var_dump($load_user_date);
    if($load_user_date['status']==200){
        add_list_task($lastid,$date,$db);
        $db->update('lastid',[],['id'=>$lastid]);
        print_r('Найден новый пользователь: id'.$lastid);
        print_r(PHP_EOL);
    }
    else if($load_user_date['status']==400 && $load_user_date['error']['message']=='User not find') {
        echo date("H:i:s").' - '.'Новых пользователей не найдено'.' '.("($lastid)").PHP_EOL;
        $сount_check_next_10++;
        if($сount_check_next_10>=10){
            echo date("H:i:s").' - '.'Делаем заход на 10+ вперед'.PHP_EOL;
            $сount_check_next_10=0;
            check_next_users($db);
        }
    }else if(($load_user_date['status']==401 && $load_user_date['error']['message']=='Token is Invalid')||$auth_token==null){
        echo "Token is Invalid. Update...".PHP_EOL;
        $params_select = [
            ['key'=>"auth_token",'value'=>$auth_token,'symb'=>"="],
        ];
       // $phone = $db->select('authorization','phone',$params_select)[0]['phone'];
        $phone = $profile['phone'];
        $new_token_auth = get_token($phone);
        (int)$phone;  
        $params_update=[
            ['key'=>"phone", 'value'=>$phone, 'symb'=>"="]
        ];
        $db->update('authorization',$params_update,['auth_token'=>$new_token_auth]);
        echo ("Update auth_token profile: $profile[model_id]".PHP_EOL);
    }
    sleep(15);
}
function add_list_task($lastid,$date,$db){
    $exit_condition=rand(18,22);
    for($i=2;$i<=$exit_condition;$i++){
        $db->insert('timetable',['user_id','type','unix','used'],[$lastid,'Visit',date("Y-m-d H:i:s",strtotime("+".((fibo($i))*60+rand(-59,59))." seconds")),'0']);
    }
    $exit_condition=rand(4,8);
    for($i=2;$i<=$exit_condition;$i++){
        $db->insert('timetable',['user_id','type','unix','used'],[$lastid,'Like',date("Y-m-d H:i:s",strtotime("+".((fibo($i))*60+rand(-59,59))." seconds")),'0']);
    }
}
function check_next_users($db){
    for($i=1;$i<10;$i++){
        $lastid = $db->select('lastid', 'id')[0]['id'];
        $lastid+=$i;
        $auth_token = get_rand_profile('ru')['auth_token'];
        $load_user_date = get_laod_user_date(($lastid),$auth_token);
        $date = date("Y-m-d H:i:s");
        if($load_user_date['status']==200){
            $db->update('lastid',[],['id'=>$lastid]);
            add_list_task($lastid,$date,$db);
            print_r('Найден новый пользователь: id'.$lastid);
            print_r(PHP_EOL);
            break;
        }
        sleep(1);
    }
}
function fibo($i) {
    if ($i == 0 ) return 0;
    if ($i == 1 || $i == 2) {
        return 1;
    } else {
        return fibo($i - 1) + fibo($i -2);
    }
}