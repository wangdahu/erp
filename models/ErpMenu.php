<?php

class ErpMenu extends CComponent{
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
            'erp/sales/index', 'erp/sales/create',
            'erp/sales/history',
            'erp/backsales/index',
            'erp/sales/view',
        ));
    }
    
    public function isBuyOrder(){
        return $this->_isMatch(array(
            'erp/buy/plan',
            'erp/buy/index', 'erp/buy/create',
            'erp/buy/history',
            'erp/backbuy/index',
            'erp/buy/view',
        ));
    }
    
    public function isStock(){
        return $this->_isMatch(array(
            'erp/stock/index', 'erp/stockin/create', 'erp/stockout/create', 'erp/stockallocate/create',
            'erp/stockin/index', 'erp/stockin/item', 'erp/stockin/back',
            'erp/stockout/index', 'erp/stockout/item', 'erp/stockout/back',
            'erp/stockallocate/index', 'erp/stockallocate/item', 'erp/stockout/view',
            'erp/product/list',
        ));
    }
    
    public function isReport(){
        return $this->_isMatch(array(
            'erp/report/sales',
        ));
    }
    
    public function isFinance(){
        return $this->_isMatch(array(
            'erp/finance/receive',
            'erp/finance/pay',
            'erp/finance/billing',
            'erp/finance/income',
            'erp/finance/expenses',
        ));
    }
    
    public function isSupplier(){
        return $this->_isMatch(array(
            'erp/supplier/index', 'erp/supplier/create',
            'erp/supplier/update',
        ));
    }
    
    public function isCustomer(){
        return $this->_isMatch(array(
            'erp/customer/index', 'erp/customer/create',
            'erp/customer/update',
        ));
    }
    
    public function isSetting(){
        return $this->_isMatch(array(
            'erp/setting/storehouse',
            'erp/setting/product',
            'erp/approve/createflow',
            'erp/approve/updateflow',
            'erp/approve/index',
        ));
    }
}
