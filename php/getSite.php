<?php

class getSite{

    public function index ($data){
        $data     =   $this->decode($data);
        return $data;
    }
    public function decode($data){
        $data['work_type']              = unserialize($data['work_type']            );
        $data['work_type']['bx_id']     = unserialize($data['work_type']['bx_id']   );
        $data['subject']                = unserialize($data['subject']              );
        $data['subject']['bx_id']       = unserialize($data['subject']['bx_id']     );
        $data['university']             = unserialize($data['university']           );
        $data['faculty']                = unserialize($data['faculty']              );
        $data['course']                 = unserialize($data['course']               );
        $data['files']                  = unserialize($data['files']                );
        $data['university']['bx_id']    = unserialize($data['university']['bx_id']  );
        $data['faculty']['bx_id']       = unserialize($data['faculty']['bx_id']     );
        $data['course']['bx_id']        = unserialize($data['course']['bx_id']      );
        //$data['files']                  = unserialize($data['files']                );
        $data['files']                  = $this->checkFile($data['files']           );

        return $data;
    }

    public function checkFile($data){
        foreach($data['data'] as $item){
            $arr[] = ['fileData' => array($item['name'], $item['file'])  ];
        }
        return $arr;
    }
}
?>