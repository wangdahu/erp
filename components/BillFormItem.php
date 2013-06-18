<?php

abstract class BillFormItem extends ActiveRecord {
    
    /**
     * 所属进销存单id
     * @return string
     */
    abstract public function belongId();
    
    protected function beforeValidate(){
        if (parent::beforeValidate()){
            if ($this->isNewRecord){
                $product = Product::model()->findByPk($this->product_id);
                $this->product_name = $product->name;
                $this->product_no = $product->no;
                $this->product_brand = $product->brand ? $product->brand->name : "";
                $this->product_unit = $product->unit->name;
                $this->product_cate = $product->cate->name;
            }
            return true;
        }
        return false;
    }
}