<?php
class ErpPrivilege{
    
    /**
     * 新添销售单
     * @var string
     */
    const SALES_ORDER_CREATE = 'sales_order_create,sales_admin';
    
    const SALES_ORDER_VIEW = 'sales_order_view,sales_admin';
    
    const SALES_BACK = 'sales_back,sales_admin';
    
    const SALES_ADMIN = 'sales_admin';
    
    const BUY_ORDER_CREATE = 'buy_order_create,buy_admin';
    
    const BUY_ORDER_VIEW = 'buy_order_view,buy_admin';
    
    const BUY_BACK = 'buy_back,buy_admin';
    
    const BUY_ADMIN = 'buy_admin';
    
    const STOCK_ADD = 'stock_add,stock_admin';
    
    const STOCK_VIEW = 'stock_view,stock_admin';
    
    const STOCK_ADMIN = 'stock_admin';
    
    const FINANCE_ADMIN = 'finance_admin';
    
    const REPORT_VIEW = 'report_view';
    
    const SUPPLIER_CREATE = 'supplier_create,supplier_admin';
    
    const SUPPLIER_VIEW = 'supplier_view,supplier_admin';
    
    const SUPPLIER_ADMIN = 'supplier_admin';
    
    const CUSTOMER_CREATE = 'customer_create,customer_admin';
    
    const CUSTOMER_VIEW = 'customer_view,customer_admin';
    
    const CUSTOMER_ADMIN = 'customer_admin';
    
    const SETTING = 'setting';
    
    
    protected static function check($key, $group, $user_id = null){
        $privileges = explode(',', $key);
        foreach ($privileges as $privilege){
            if (Privilege::check('erp', $group, $privilege, $user_id)){
                return true;
            }
        }
        return false;
    }
    
    public static function salesCheck($key, $user_id = null) {
        return self::check($key, 'sales', $user_id);
    }
    
    public static function buyCheck($key, $user_id = null) {
        return self::check($key, 'buy', $user_id);
    }
    
    public static function stockCheck($key, $user_id = null) {
        return self::check($key, 'stock', $user_id);
    }
    
    public static function customerCheck($key, $user_id = null) {
        return self::check($key, 'customer', $user_id);
    }
    
    public static function supplierCheck($key, $user_id = null) {
        return self::check($key, 'supplier', $user_id);
    }
    
    public static function otherCheck($key, $user_id = null) {
        return self::check($key, 'other', $user_id);
    }
    
}
