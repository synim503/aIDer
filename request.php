<?php
include_once './db/DB.php';
$db = new DB();

function get_token($phone_number){
    $phone_number=substr($phone_number,1);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://*****.com/api/v1/check_s*****n_register',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('rid' => '*****',  'phone_code' => '+1','phone_number' => "{$phone_number}",'sms_code' => '0000'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ***'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $response_decode = json_decode($response, true);
    if($response_decode["data"]["auth_token"]){
        return $response_decode["data"]["auth_token"];
    }else {
        print_r('Призошла ошибка с получением auth_token для номера ' . $phone_number . '.' . PHP_EOL . 'Сообщение с ошибкой: "' . $response_decode['error']['message'] . "\"");
    }
}

function get_notifications($auth_key){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://*****.com/api/v1/notifications',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('type' => '','last_id' => ''),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$auth_key
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
}

function make_visit($goid, $auth_key){

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://*****.com/api/v1/make_visit',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('goid' => $goid),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$auth_key
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
}

function make_like($goid,$auth_key){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://*****.com/api/v1/make_like',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('goid' => $goid),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$auth_key
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
}

function get_rand_profile($tag){
    global $db;
    $rand_profile=$db->select_rand_value("authorization");
    return $rand_profile;

}
function get_laod_user_date($id, $auth_token){

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://*****.com/api/v1/load_user_data',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('id' => (int)$id),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$auth_token
        ),
    ));

    $response = curl_exec($curl);
   // var_dump($response);
    curl_close($curl);
    $response_decode = json_decode($response, true);
    return $response_decode;
}

function get_number_from_file($tag){
    $text = fopen("numbers/$tag.txt", "r");
    $array = null;
    if ($text) {
        while (($buffer = fgets($text)) !== false) {
            $array[] = $buffer;
        }
    }
    fclose($text);
    return $array;
}

function get_auth_token_from_file($tag){
    $numbers = get_number_from_file($tag); //массив возвращаймый функцией get_number_token_file из файла, содержит список номеров
    foreach ($numbers as $number){
        //$token = get_token($number);
    }
}
