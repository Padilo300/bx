<?php
require_once(__DIR__.'/transferData.php');

class essenceAdd extends transferData {
    /* 
        Этот класс для добавления/редактирования/обновления сущностей.
        По конструкту требует url вебхука
    */

    /* методы */
    const   methodAdd         = 'crm.lead.add.json'       ;
    const   methodUpdata      = 'crm.lead.update.json'    ;
    const   listUserfiledList = 'crm.lead.userfield.list' ;

    const   contactAdd        = 'crm.contact.add'         ;
    const   contactGet        = 'crm.contact.get'         ;
    
    const   leadGet           = 'crm.lead.get'            ;
    const   dealAdd           = 'crm.deal.add'            ;
    const   dealGet           = 'crm.deal.get'            ;
    
    
    /* переменные для обращения CURL_url */
    public  $add              = null; 
    public  $upData           = null; 
    public  $userFieldList    = null; 
    public  $leadGet          = null; 
    public  $contactAdd       = null; 
    public  $dealAdd          = null;


    public function __construct($url){
        $this->add              = $url . self::methodAdd         ;
        $this->upData           = $url . self::methodUpdata      ;

        $this->userFieldList    = $url . self::listUserfiledList ;
        $this->leadGet          = $url . self::leadGet           ;

        $this->contactAdd       = $url . self::contactAdd        ;
        $this->contactGet       = $url . self::contactGet        ;

        $this->dealAdd          = $url . self::dealAdd           ;
        $this->dealGet          = $url . self::dealGet           ;
        
    }
    
    public function leadGet($id){
        $data = array(
            'id'    => $id,
        );
        return parent::curlStart($this->leadGet, $data);
    }

    public function contactGet($id){
        $data = array(
            'id'    => $id,
        );
        return parent::curlStart($this->contactGet, $data);
    }

    public function contactAdd($data){
        $data = array(
            "fields" => $data,
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        );
        return parent::curlStart($this->contactAdd, $data);
    }

    public function add($data,$phone){
        $data = array(
            'fields' => $data,
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        );

        if(is_numeric($phone)){
            $data['fields']['PHONE'] = array(array("VALUE" => $phone, "VALUE_TYPE" => "WORK"));
        }
        $result =  parent::curlStart($this->add, $data);

        if(!$result['result'] > 1){
            $result = FALSE;
        }
        return $result;
    }

    public function listUserfiled(){
        $data = array(
            'order'     => array( "SORT"        => "ASC" ),
            'filter'    => array( "MANDATORY"   => "N"   )
        );
        return  parent::curlStart($this->userFieldList, $data)  ;
    }

    public function leadUpdata($id, $arr){
        $data = array(
            'id'        =>  $id,
            'fields'    =>  $arr,
            'params'    =>  array("REGISTER_SONET_EVENT" => "Y")
        );
        return parent::curlStart($this->upData, $data);
    }

    public function dealAdd($data, $phone){
        $data = array(
            'fields' => $data,
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        );

        $result =  parent::curlStart($this->dealAdd, $data);

        if(!$result['result'] > 1){
            $result = FALSE;
        }
        return $result;
    }

    public function dealGet($id){
        $data = array(
            'id'    => $id,
        );
        return parent::curlStart($this->dealGet, $data);
    }
}
?>