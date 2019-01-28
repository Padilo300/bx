<?php

echo "<pre>";
/* 
ini_set('error_reporting',        E_ALL );
ini_set('display_errors',         1     );
ini_set('display_startup_errors', 1     ); */

require_once(__DIR__ . '/php/failData.php'       ); // ложные данные
require_once(__DIR__ . '/php/transferData.php'   ); // CURL
require_once(__DIR__ . '/php/getSite.php'	     ); // Декодирует данные
require_once(__DIR__ . '/php/userField.php'      ); // Создание пользовательских полей
require_once(__DIR__ . '/php/essence.php'        ); // Создание лида
require_once(__DIR__ . '/php/getFields.php'      ); // Получить необходимые id полей

function pr($var) {
    echo '<pre style="    background: black;
    padding: 15px;
    color: #0f0;
    font-size: 15px;
    font-weight: 500;
    font-family: monospace;
    width: max-content;">';
    var_dump($var);
    echo '</pre>';
}

function get($url){
    if( $curl = curl_init() ) {
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2';
        curl_setopt( $curl, CURLOPT_USERAGENT, $userAgent );
        curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);

        return $out  ;
    }
}

$webHookScript = 'тут твой вэбхук должен быть';
$webHookPadilo = 'тут твой вэбхук должен быть';

$userfield  = 	new userField($webHookScript)	;
$eseence	=   new essenceAdd($webHookScript)	;
$dataSite   =   new getSite                     ;
$getFields	=	new getFields($webHookScript )	;
$CURL       =   new transferData                ;

$json = file_get_contents('php://input');

$CURL->writeToLog($json, '$json'	);
$CURL->writeToLog($_POST, '$_POST'	);

//$data = $data2 = $arr;

$data = $_POST;

$data = $dataSite->index($data); // unserialize data

/* проверям заполненность полей, если не заполненно, скажем о этом */
function is_empty($var){
	if(!isset($var) || $var == '')  {
		$var = 'поле не заполненно';
	}
	return $var;
}

if($data['is_email_confirmed'] == 0){

/* если  нам нужно создать лида */        

    $theme              =   is_empty($data['theme'])     ; // тема обращения (будем использовать как заголовок лида)
    $comment            =   is_empty($data['comment'])   ; // комментарий заголовка
    $firstName          =   is_empty($data['user_name']) ; // Имя лида
    $email              =   is_empty($data['email'])     ;
    $phone              =   is_empty($data['phone'])     ;
    $files              =   is_empty($data['files']['count_files']) ; // Кол-во файлов
    $filesData          =   is_empty($data['files']['data'])        ; // Файл
    $email_confirmed    =   is_empty($data['is_email_confirmed'])   ; // подтвердение email

    $leadData = array(
        "TITLE"                     => 'Новая заяка с сайта' ,
        "NAME"                      => $firstName            ,
        "LAST_NAME"                 => 'Не указано'          ,
        "STATUS_ID"                 => "NEW"                 ,
        "OPENED"                    => "Y"                   ,
        "EMAIL"                     => array(array("VALUE" =>  $email, "VALUE_TYPE" => "WORK")),
        'UF_FILE'                   => $data['files']
    );

    // Создаем лида
    $result = $eseence->add($leadData, $data['phone']);

    // Даем ответ на сайт в виде ID пользователя
    /* это тебе может не понадобится */
    if ($result['result']) {
        $data = array(
                    'BX_ID'  =>  $result['result'],
                    'number' =>  $data['number']
                );
        $api = "http://site.com";
        $CURL->curlStart($api, $data);
    }


}elseif($data['is_email_confirmed'] == 1 && $data['is_new_user'] == 1 ){

    /* ====== создаем Контакт ====  */

    $LEAD_ID   = $data['bx_id'];

    /* ======== создаем контакт ==========  */

    $theme              =   is_empty($data['theme'])                ; // тема обращения (будем использовать как заголовок лида)
    $comment            =   is_empty($data['comment'])              ; // комментарий заголовка
    $firstName          =   is_empty($data['user_name'])            ; // Имя лида
    $email              =   is_empty($data['email`'])               ;
    $phone              =   is_empty($data['phone'])                ;
    $files              =   is_empty($data['files']['count_files']) ; // Кол-во файлов
    $email_confirmed    =   is_empty($data['is_email_confirmed'])   ; // подтвердение email

    $CURL->writeToLog($data, '$dataContact unserialize'	);

    $dataContact = array(
                    'TITLE'                     => 'Создан автоматически через сайт'     ,
                    "NAME"                      =>  $firstName                           , 
                    "SECOND_NAME"               =>  ''                                   , 
                    "LAST_NAME"                 =>  ''                                   , 
                    "OPENED"                    =>  "Y"                                  , 
                    "ASSIGNED_BY_ID"            =>  $result['ASSIGNED_BY_ID']            , // ID пользователя создавшего лид
                    "LEAD_ID"                   =>  $LEAD_ID                             , // Если Вам не нужно связывать контакт с лидом / отправьте $id = false; 
                    "TYPE_ID"                   =>  "CLIENT"                             ,
                    "SOURCE_ID"                 =>  "WEB"                                ,
                    "PHONE"                     =>  array(array("VALUE" => $data['phone'], "VALUE_TYPE" => "WORK")),
                    "UF_TYPEC"                  => '26'                                  ,
                    'COMMENTS'                  => $comment                         ,
                    'UF_PROMO'                  => $data['promocode']               ,
                    'UF_FILE'                   => $data['files']
                );

    $result = $eseence->contactAdd($dataContact);
    
    /* ==== ниже логика создания сделки для только-что созданного контакта */
    if($result['result']){
        $CONTACT_ID  = $result['result'];                   // получили ид контакта
        $dataContact = array(
            "type"                      => 'contact'                   ,
            "user_BX_ID"                =>  $CONTACT_ID                ,
            "NAME"                      =>  $firstName                 , 
            "TYPE_ID"                   =>  "CLIENT"                   ,
            "PHONE"                     =>  $data['phone']             ,
            "UF_UNIVER"                 =>  $data['university']['id']  , // University
            "UF_FCT"                    =>  $data['faculty']['id']     , // Faculty
            "UF_KURS"                   =>  $data['course']['id']      , // Course
        );

        /* отправили на сайт нового пользователя  */
        $api         = "http://сайт.ком";
        $CURL->curlStart($api, $dataContact);

        /* переводим лида */
        $leadData    = array(
            'STATUS_ID'      => 'CONVERTED',
            'TITLE'          => 'Регистрация с сайта подтверждена!' ,
            'CONTACT_ID'     => $CONTACT_ID,
        );
        
        // ====> перевод лида на мах стадию
        $result = $eseence->leadUpdata($LEAD_ID, $leadData)  ;
        $theme = null;

        if($data['count_tasks'] != 0){
            $theme = $data['count_tasks'];
        }else{
            $theme = $data['work_type']['title'];
        }
        
        if($result['result']){
            /* Если все ок, готовим массив для сделки */
           $dealData   =   array(
                "TITLE"                     => "Заказ с сайта"                            , 
                "TYPE_ID"                   => "GOODS"                                    , 
                "STAGE_ID"                  => "NEW"                                      , 					
                "CONTACT_ID"                => $CONTACT_ID                                ,
                "OPENED"                    => "Y"                                        , 
                "ASSIGNED_BY_ID"            => 1                                          , 
                "PROBABILITY"               => 30                                         ,
                "CURRENCY_ID"               => "UAH"                                      , 
                "SOURCE_ID"                 => "WEB"                                      ,
                "UF_TYPEC"                  => '26'                                       ,
                'UF_FILE'                   => $data['files']                             ,
            );

            /* созадем сделку */
            $result = $eseence->dealAdd($dealData);
            
            if($result['result']){
                $dealData = $eseence->dealGet($result['result'] ); 
                $CURL->writeToLog($dealData, 'отправил на сайт сделку'	    );  

                $api = "http://test.zachet.com.ua/api/bitrix/create-order";
                $CURL->curlStart($api, $dealData);
            }else{
                $CURL->writeToLog($result, 'не удалось создать сделку и отправить на сайт'	    );  
            }
        }    
    }
}elseif($data['is_email_confirmed'] == 1 && $data['is_new_user'] == 0){

    /* =========== тут просто создаем сделку    */
    $CONTACT_ID   = $data['bx_id'];

    $dealData   =   array(
        "TITLE"                     => "Заказ с сайта"                            , 
        "TYPE_ID"                   => "GOODS"                                    , 
        "STAGE_ID"                  => "NEW"                                      , 					
        "CONTACT_ID"                => $CONTACT_ID                                ,
        "OPENED"                    => "Y"                                        , 
        "ASSIGNED_BY_ID"            => 1                                          , 
        "PROBABILITY"               => 30                                         ,
        "CURRENCY_ID"               => "UAH"                                      , 
        "SOURCE_ID"                 => "WEB"                                      ,
        "UF_TYPEC"                  => '26'                                       ,
        'UF_FILE'                   => $data['files']                             ,
    );

    $CURL->writeToLog($dealData, 'дамп сделки');  

    $result = $eseence->dealAdd($dealData);

    
}
?>