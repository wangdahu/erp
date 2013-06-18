<?php

class Approval extends CComponent{

    private $_form;
    
    public function __construct(BillForm $form){
        $this->_form = $form;
    }

    public function getIsStart(){
        $this->_form->approvalStatus() > 0;
    }

    public function getIsComplete(){
        $this->_form->approvalStatus() == 2;
    }

    public function start(){
        //$this->beforeStart();
        $this->_start();
        $this->_form->approvalStart();
    }
    
    private function _start(){
        
    }
    private function _pass(){
        
    }
    private function _fail(){
        
    }

    public function pass(){
        $this->_pass();
        $this->_form->approvalPass();
    }

    public function fail(){
        $this->_fail();
        $this->_form->approvalFail();
    }
    
/*     protected function beforeStart()
    {
        if($this->hasEventHandler('onBeforeStart'))
            $this->onBeforeStart(new CEvent($this));
    }
    
    public function onBeforeStart($event)
    {
        $this->raiseEvent('onBeforeStart',$event);
    }
    
    protected function afterStart()
    {
        if($this->hasEventHandler('onAfterStart'))
            $this->onAfterStart(new CEvent($this));
    }
    
    public function onAfterStart($event)
    {
        $this->raiseEvent('onAfterStart',$event);
    } */
}