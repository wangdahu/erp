<?php

class PssMenu extends CComponent{
    /**
     * @var CController
     */
    private $_controller;
    
    /**
     * @param CController $controller
     */
    public function __construct($controller){
        $this->_controller = $controller;
    }
    
    public function getController(){
        return $this->_controller;
    }
    
    /**
     * 
     * @param array $routes
     */
    private function _isMatch($routes){
        return in_array($this->getController()->route, $routes);
    }
    
    public function isSalesOrder(){
        return $this->_isMatch(array(
            'pss/sales/index', 'pss/sales/create',
            'pss/sales/history',
            'pss/backsales/index',
            'pss/sales/view',
        ));
    }
    
    public function isBuyOrder(){
        return $this->_isMatch(array(
            'pss/buy/plan',
            'pss/buy/index', 'pss/buy/create',
            'pss/buy/history',
            'pss/backbuy/index',
            'pss/buy/view',
        ));
    }
    
    public function isStock(){
        return $this->_isMatch(array(
            'pss/stock/index', 'pss/stockin/create', 'pss/stockout/create', 'pss/stockallocate/create',
            'pss/stockin/index', 'pss/stockin/item', 'pss/stockin/back',
            'pss/stockout/index', 'pss/stockout/item', 'pss/stockout/back',
            'pss/stockallocate/index', 'pss/stockallocate/item', 'pss/stockout/view',
            'pss/product/list',
        ));
    }
    
    public function isReport(){
        return $this->_isMatch(array(
            'pss/report/sales',
        ));
    }
    
    public function isFinance(){
        return $this->_isMatch(array(
            'pss/finance/receive',
            'pss/finance/pay',
            'pss/finance/billing',
            'pss/finance/income',
            'pss/finance/expenses',
        ));
    }
    
    public function isSupplier(){
        return $this->_isMatch(array(
            'pss/supplier/index', 'pss/supplier/create',
            'pss/supplier/update',
        ));
    }
    
    public function isCustomer(){
        return $this->_isMatch(array(
            'pss/customer/index', 'pss/customer/create',
            'pss/customer/update',
        ));
    }
    
    public function isSetting(){
        return $this->_isMatch(array(
            'pss/setting/storehouse',
            'pss/setting/product',
            'pss/approve/createflow',
            'pss/approve/updateflow',
            'pss/approve/index',
        ));
    }
}
