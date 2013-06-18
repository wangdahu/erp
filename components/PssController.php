<?php

class PssController extends Controller {
    public $layout = 'main';
    
    public function init(){
        parent::init();
        $this->breadcrumbs['进销存管理'] = array('/pss');
        $this->activeMenu = 'pss';
    }
    
    public function beforeAction($action){
        if (parent::beforeAction($action)){
            $request = Yii::app()->request;
            if ($request->isAjaxRequest){
                Yii::app()->clientScript->scriptMap['jquery.js'] = false;
            }
            $pssMenu = new PssMenu($this);
            $this->navMenu = array(
                    array('label' => '销售单', 'url' => array('/pss/sales'), 'active'=>$pssMenu->isSalesOrder()),
                    array('label' => '采购单', 'url' => array('/pss/buy'), 'active'=>$pssMenu->isBuyOrder()),
                    array('label' => '货品库存', 'url' => array('/pss/stock'), 'active'=>$pssMenu->isStock()),
                    array('label' => '报表统计', 'url' => array('/pss/report'), 'active'=>$pssMenu->isReport()),
                    array('label' => '财务管理', 'url' => array('/pss/finance'), 'active'=>$pssMenu->isFinance()),
                    array('label' => '供应商管理', 'url' => array('/pss/supplier'), 'active'=>$pssMenu->isSupplier()),
                    array('label' => '客户管理', 'url' => array('/pss/customer'), 'active'=>$pssMenu->isCustomer()),
                    array('label' => '设置', 'url' => array('/pss/setting'), 'active'=>$pssMenu->isSetting()),
            );
            return true;
        }
        return false;
    }
    
    public function getDeptOptions(){
        return Account::departmentDropdown();
    }
    
    public function getUserOptions($deptId=0, $showAll=false){
        if ($deptId>0){
            return CHtml::listData(PssUser::model()->department($deptId)->findAll(), 'id', 'name');
        }elseif ($showAll){
            return CHtml::listData(PssUser::model()->findAll(), 'id', 'name');
        }else{
            return array();
        }
    }
}

?>