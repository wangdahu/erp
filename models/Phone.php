<?php

class Phone extends CComponent{
    
    private $_type;
    private $_no;
    
    public function __construct($no, $type){
    
    }
    
    public function getType(){
        return $this->_type;
    }
    
    public function getNumber(){
        return $this->_no;
    }
}