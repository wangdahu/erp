<?php
abstract class AppModel extends CModel
{
    
    private $_id;
    

    private $_d;
    
    private $_attributes;
    
    private static $_models;
    
    
    public function __construct($scenario=null){
        if ($scenario !== null){
            $this->setScenario($scenario);
        }
    }
    
    /**
     * @param string $className
     * @return AppModel
     */
    public static function model($className=__CLASS__){
        if(isset(self::$_models[$className])){
            return self::$_models[$className];
        }else{
            $model=self::$_models[$className]=new $className(null);
            return $model;
        }
    }
    
    
    public function getId(){
        return $this->_id;
    }
    
    /**
     * @see Account::users();
     */
    public function getData(){
        if ($this->_d === null){
            $this->_d = $this->dataProvider();
        }
        return $this->_d;
    }
    
    public function setData(array $data){
        $this->_d = $data;
    }
    
    /**
     * @return array
     */
    abstract protected function dataProvider();
    

    public function findAll(){
        $instantiates=array();
        foreach ($this->getData() as $data){
            $instantiates[] = $this->instantiate((array)$data);
        }
        return $instantiates;
    }
    
    public function find($id){
        $data = $this->getData();
        return $this->instantiate((array)$data[$id]);
    }
    
    protected function instantiate($data){
        $class=get_class($this);
        $model=new $class(null);
        $model->_id = $data['id'];
        $model->populateData($data);
        return $model;
    }
    
    protected function populateData(array $data){
        $this->_attributes = $data;
    }
    
    public function __get($name){
        $attributes = $this->_attributes;
        if (array_key_exists($name, $attributes)){
            return $attributes[$name];
        }
        return parent::__get($name);
    }

    
    /* (non-PHPdoc)
     * @see CModel::attributeNames()
     */
    public function attributeNames() {
        // TODO Auto-generated method stub
        return array_keys($this->_attributes);
    }

}