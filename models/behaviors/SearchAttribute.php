<?php

class SearchAttribute extends CModelBehavior{
    
    
    public $date_pattern, $start_date, $end_date, $dept_id, $keyword, $approval_status;
    
    public function compareDate($column){
        $criteria = new CDbCriteria();
        if (isset($this->date_pattern) && is_string($this->date_pattern)){
            switch ($this->date_pattern){
                case "0":
                    $criteria->compare("FROM_UNIXTIME({$column}, '%Y-%m-%d')", date("Y-m-d"));
                    break;
                case "1":
                    $criteria->compare("FROM_UNIXTIME({$column}, '%Y-%u')", date("Y-W"));
                    break;
                case "2":
                    $criteria->compare("FROM_UNIXTIME({$column}, '%Y-%m')", date("Y-m"));
                    break;
                case "3":
                    $criteria->compare("FROM_UNIXTIME({$column}, '%Y-%m-%d')", ">={$this->start_date}");
                    $criteria->compare("FROM_UNIXTIME({$column}, '%Y-%m-%d')", "<={$this->end_date}");
                    break;
            }
        }
        return $criteria;
    }
    
    public function compareApprovalStatus($approval_status){
        $criteria = new CDbCriteria();
        if(!empty($approval_status)){
            $criteria->condition = "select * from core_flow";
        }
        return $criteria;
    }

    public static function getDatePatternOptions(){
        return array('今天', '本周', '本月', '时间范围');
    }
    
    
/*    public function getCustomAttributes(){
        return $this->_attributes;
    }
    
    protected function setAttributes($attributes){
        $this->_attributes = $attributes;
    }
    

    
    public function isGetter($method){
        return substr($method, 0, 3) == "get";
    }
    
    public function isSetter($method){
        return substr($method, 0, 3) == "set";
    }
    
    public function __set($name, $value){
        if (isset($this->_attributes[$name])){
            $this->_attributes[$name] = $value;
        }
        parent::__set($name, $value);
    }
    
    public function __get($name){
        var_dump($this->_attributes[$name], isset($this->_attributes[$name]));
        if(isset($this->_attributes[$name])){
            echo $name;exit;
            return $this->_attributes[$name];
        }
        parent::__get($name);
    }
    
    
    public function __isset($name)
    {
        if(isset($this->_attributes[$name])){
            return true;
        }
        return parent::__isset($name);
    }
    
    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     */
    /*
    public function __unset($name)
    {
        if(isset($this->_attributes[$name])){
            unset($this->_attributes[$name]);
        }
        parent::__unset($name);
    }*/
}