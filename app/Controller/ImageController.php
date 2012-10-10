<?php
class ImageController extends AppController{
    //Helpers a usar
    public $helpers = array('Html','Ajax','Javascript','Session','Renderer');

    //Componentes a usar
    public $components = array('RequestHandler', 'Session', 'Thumbnail');
    
    
    public function index($data){
        //debug($data);
    }
    
    public function thumb(){
        $this->autoRender = false;
        $dir = '';
        $w = 0;
        $h = 0;
        $zc = 0;
        //Configure::write('debug', 1);
        //debug($this->params['pass']);
        //foreach ($this->params['pass'] as $key=>$value){
        //    if ($key<=4){
        //        $concat = '';
        //        if ($key>0){
        //            $concat = '/';
        //        }
        //        $dir .= $concat.$value;
        //    } else {
        //        switch ($key){
        //            case 5:
        //                $w = $value;
        //                break;
        //            case 6:
        //                $h = $value;
        //                break;
        //            case 7:
        //                $zc = $value;
        //                break;
        //        }
        //    }
        //}
        foreach ($this->params['pass'] as $key=>$value){
            if ($key<(count($this->params['pass'])-4)){
                $concat = '';
                if ($key>0){
                    $concat = '/';
                }
                $dir .= $concat.$value;
            }
        }
        $w = $this->params['pass'][count($this->params['pass'])-4];
        $h = $this->params['pass'][count($this->params['pass'])-3];
        $zc = $this->params['pass'][count($this->params['pass'])-2];
        $opt = $this->params['pass'][count($this->params['pass'])-1];
        $externo = '';
        if ($opt=='1'){
            $externo = 'http://';
        }
        $this->Thumbnail->width = $w;
        $this->Thumbnail->height = $h;
        $this->Thumbnail->zc = $zc;
        if (!$this->Thumbnail->displayThumbnail($externo.$dir, false, true)){
            debug($this->Thumbnail->errors);
        }
    }
    
    
}
?>