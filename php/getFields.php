<?php
require_once(__DIR__.'/transferData.php');
require_once(__DIR__.'/userField.php'   );

class getFields extends userField {
    /* 
        Этот класс принмает массив данных
        Берет только нужные ключи массива (Универ, тип работы и тд..)
        Сопостовляет заявленные ID с ID битрикса
        Возрващает короткий массив в виде ТИП ПОЛЯ => ID в битриксе
    */
    public function __construct($url){
        parent::__construct($url);

    }
        
    public function index($data){

        /* Если емейл не подтвержен */
        if($data['is_email_confirmed'] == 0){
            $data = $this->arraySplit($data); // получаем массив с необходимыми полями

            $data[0][1] = $this->listWork_type ($data[0][1]);
            $data[1][1] = $this->listSubject   ($data[1][1]);
            $data[2][1] = $this->listUniversity($data[2][1]);
            $data[3][1] = $this->listFaculty   ($data[3][1]);
            $data[4][1] = $this->listCourse    ($data[4][1]);

            return $data;
        }

    }

    public function arraySplit($data){
        $name = array_keys ($data);
        $arr  = array(
                        array($name[1] , $data['work_type'][0]  ) ,
                        array($name[2] , $data['subject'][0]    ) ,
                        array($name[14], $data['university'][0] ) ,
                        array($name[15], $data['faculty'][0]    ) ,
                        array($name[16], $data['course'][0]     ) ,
                );
        return $arr;
    }
    
    public function listWork_type ($id){
        if(1){
            return '107';
        }
         }
    public function listSubject ($id){
        if(1){
            return '524';
        }
    }
    public function listUniversity ($id){
        if(1){
            return '206';
        }

    }
    public function listFaculty ($id){
        if(1){
            return '285';
        }
    }
    public function listCourse ($id){
        if(1){
            return '196';
        }
    }

}

?>