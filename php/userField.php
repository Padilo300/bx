<?
require_once(__DIR__.'/transferData.php');

class userField extends transferData{
    /* 
    Этот класс принимает данные для создания пользовательского поля
    По конструкту требует url вебхука

        $FIELD_NAME          = 'theme'                  ;
        $EDIT_FORM_LABEL     = 'отобржаемое имя поля'   ;
        $LIST_COLUMN_LABEL   = 'Не отобржаемое имя поля ';
        $USER_TYPE_ID        = 'string'; тип поля НИЖЕ СПИСОК ВОЗМОЖНЫХ ВАРИАНТОВ 
                                                    money                    Деньги
                                                    disk_file                Файл (Диск)
                                                    disk_version             Версия файла (Диск)
                                                    video                    Видео
                                                    mail_message             Письмо (email)
                                                    hlblock                  Привязка к элементам highload-блоков
                                                    webdav_element           Документ из библиотеки документов
                                                    webdav_element_history   Документ истории из библиотеки документов
                                                    crm                      Привязка к элементам CRM
                                                    crm_status               Привязка к справочникам CRM
                                                    employee                 Привязка к сотруднику
                                                    string                   Строка
                                                    integer                  Целое число
                                                    double                   Число
                                                    datetime                 Дата со временем
                                                    date                     Дата
                                                    boolean                  Да/Нет
                                                    address                  Адрес
                                                    url                      Ссылка
                                                    file                     Файл
                                                    enumeration              Список
                                                    iblock_section           Привязка к разделам инф. блоков
                                                    iblock_element           Привязка к элементам инф. блоков
                                                    string_formatted         Шаблон
                                                    vote                     Опрос
                                                    url_preview              одержимое ссылки

    */
    const   methodAdd              = 'crm.lead.userfield.add'      ;
    const   methodUpdata           = 'crm.lead.userfield.update'   ;
    const   methodList             = 'crm.lead.userfield.list'     ;
    const   methodDelete           = 'crm.lead.userfield.delete'   ;    
    const   methodGet              = 'crm.lead.userfield.get'      ;

    const   contactMethodAdd       = 'crm.contact.userfield.add'   ;
    const   contactMethodUpdata    = 'crm.contact.userfield.update';
    const   contactMethodList      = 'crm.contact.userfield.list'  ;    
    const   contactGet             = 'crm.contact.userfield.get'   ;    
    const   contactdelete          = 'crm.contact.userfield.delete';

    
    public  $leadAdd           = null; 
    public  $leadList          = null; 
    public  $leadUpdata        = null;
    
    public  $contactAdd        = null; 
    public  $contactList       = null; 
    public  $contactUpdata     = null;

    /* По конструкту требует ссылку на вебхук */
    public function __construct($url){
        $this->leadAdd         = $url . self::methodAdd            ;
        $this->leadUpdata      = $url . self::methodUpdata         ;
        $this->leadList        = $url . self::methodList           ;
        $this->leadDelete      = $url . self::methodDelete         ;
        $this->leadGet         = $url . self::methodGet            ;

        $this->contactAdd      = $url . self::contactMethodAdd     ;
        $this->contactUpdata   = $url . self::contactMethodUpdata  ;
        $this->contactList     = $url . self::contactMethodList    ;
        $this->contactGet      = $url . self::contactGet           ;
        $this->contactDelete   = $url . self::contactdelete        ;
    }
    
    /* === методы для работы с полями ЛИДОВ  === */
    public function leadAdd($FIELD_NAME ,$EDIT_FORM_LABEL ,$LIST_COLUMN_LABEL ,$USER_TYPE_ID){
        $arr = array(
            "fields" => array(
                "FIELD_NAME"        => 'UF_CRM_'.$FIELD_NAME    , // уникальный ID 
                "EDIT_FORM_LABEL"   => $EDIT_FORM_LABEL         ,
                "LIST_COLUMN_LABEL" => $LIST_COLUMN_LABEL       ,
                "USER_TYPE_ID"      => $USER_TYPE_ID            ,
                "MULTIPLE"          => "Y"
            ) 
        );
        return parent::curlStart($this->leadAdd, $arr); 
    }
    
    public function leadUpdata($id, $arr ){    
        $data = array(
                'id' => $id,
                $arr,
        );
        return parent::curlStart($this->leadUpdata, $data);
    }

    public function leadDelete($id){
        $data = array(
                'id' => $id,
        );
        return parent::curlStart($this->leadDelete, $data);
    }   

    public function leadList(){
        $data = array(
            'order'  =>  array("SORT"      => "ASC" ),
        );
        return parent::curlStart($this->leadList, $data);
    }

    public function leadGet($id){
        $data = array(
            'id' => $id 
        );
        return parent::curlStart($this->leadGet, $data);
    }

    /*  === методы для работы с полями КОНТАКТОВ  === */

    public function contactAdd($data){
        $arr = array(
            "fields" => $data
        );
        return parent::curlStart($this->contactAdd, $arr);
    }

    public function contactUpdata($id, $arr ){
        $data = array(
                'id'        => $id,
                'fields'    => $arr
        );
        $this::writeToLog($data, 'Массив при обновлении' );
        return parent::curlStart($this->contactUpdata, $data);
    }   

    public function contactList(){
        $data = array(
            'order'  =>  array("SORT"      => "ASC" ),
            'filter' =>  array("MANDATORY" => "N")
        );
        return parent::curlStart($this->contactList, $data);
    }

    public function contactGet($id){
        $data = array(
            'id' => $id 
        );
        return parent::curlStart($this->contactGet, $data);
    }

    public function contactListIteamUpdata($listID, $iteamID, $newValue){
        /* 
            Обновить элемент списка (VALUE)
            принимает: 
            - ID списка 
            - ID элемента который нужно обновить
            - Новое значение
        */

        if( !empty($listID)  && !empty($iteamID)  && !empty($newValue) ){

            $listID     = trim(urldecode(htmlspecialchars($listID    ))); // чистим\защищаем входящие данные
            $iteamID    = trim(urldecode(htmlspecialchars($iteamID   ))); // чистим\защищаем входящие данные
            $newValue   = trim(urldecode(htmlspecialchars($newValue  ))); // чистим\защищаем входящие данные
        
            $list       = $this->contactGet($listID)                    ;  // получаем весь список со всеми значениями  
            
            /* ищем в листе элемент который нужно обновить */
            foreach($list['result']['LIST'] as &$iteam){
                if($iteam['ID'] == $iteamID){
                    $iteam['VALUE'] = $newValue;/* Заменяем значение */
                }
            }

            $data   = ['LIST' => $list['result']['LIST'] ]  ; // вид массива для передачи на обновление
            $result = $this->contactUpdata($listID, $data)  ; // обновляем список 

            if($result['result']){
                echo 'Успешно обновленно!';
            }else{
                echo 'Не удалось обновить.';
            }
        }else{
            echo 'Не удалось обновить, возможно не были переданны все необходимые значения.';
        }
    }

    public function contactListIteamDelete($listID, $iteamID){
        /* 
            Удалить элемент списка 
            принимает: 
            - ID списка 
            - ID элемента
        */
        if( !empty($listID)  && !empty($iteamID) ){

            $listID        = trim(urldecode(htmlspecialchars($listID    ))); // чистим\защищаем входящие данные
            $iteamID       = trim(urldecode(htmlspecialchars($iteamID   ))); // чистим\защищаем входящие данные
            $iteamNum      = 0                                             ; // счетчик
            
            // 1) получаю элемент
            $list          = $this->contactGet($listID)                    ; // получаем весь список со всеми значениями  
            
            // 2) Обновляем список (удаляем элемент)
            foreach($list['result']['LIST'] as &$iteam){
                if($iteam['ID'] == $iteamID){
                    unset($list['result']['LIST'][$iteamNum]);
                    break;
                }
                $iteamNum += 1;
            }

            // 3) сохранить ID
            $updateID = array(
                'ID'        => $list['result']['ID'],
                'LIST'      => $list['result']['LIST']
            );

            // 4) Удалить поле
            $resultDelete = $this->contactDelete($listID);

            // 5) Создаем пользовательское поле
            if($resultDelete['result']){
                $resultAdd = $this->contactAdd($list['result']); // Вернет ID нового поля
                if($resultAdd['result']){
                    echo "новый ИД " . $resultAdd['result']  . ". старый ИД = " . $list['result']['ID'];
                    $resultUpd = $this->contactUpdata($resultAdd['result'],$updateID); // Присваивает полю его родной ID
                }
            }
            
            if($resultUpd['result']){
                echo "Успешно удаленно!";
            }else{
                echo 'Не удалось удалить.';
            }
        }else{
            echo 'Не удалось удалить, возможно не были переданны все необходимые значения.';
        }
    }

    public function contactListIteamAdd($listID, $iteam){

        if( !empty($listID) && !empty($iteam) ){

            $listID     = trim(urldecode(htmlspecialchars($listID    ))); // чистим\защищаем входящие данные
            $iteam      = trim(urldecode(htmlspecialchars($iteam     ))); // чистим\защищаем входящие данные

            // 1) получаю элемент
            $list          = $this->contactGet($listID) ; // получаем весь список со всеми значениями  

            // 2) готовим новый элемент
            $data          = array(
                                    'SORT'  => '10',
                                    'VALUE' => $iteam,
                                    'DEF'   => 'N'
                            ); 

            // 3) Добавляем новый элемент в конец листа
            $list['result']['LIST'][] = $data;
            $data = array(
                       'LIST' => $list['result']['LIST']
                    );
            $result = $this->contactUpdata($listID, $data)  ; // обновляем список 

            if($result['result']){
                echo 'true';
            }else{
                echo 'Не удалось добавить элемент.';
            }
        }else{
            echo 'Не удалось добавить элемент, возможно не были переданны все необходимые значения.';
        }
    }

    public function contactDelete($id){
        $data = array(
            'id' => $id 
        );
        return parent::curlStart($this->contactDelete, $data);
    }
}
?>