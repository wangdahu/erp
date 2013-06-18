<?php

/**
 * This is the model class for table "erp_storehouse".
 *
 * The followings are the available columns in table 'erp_storehouse':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $storeman_id
 * @property integer $user_id
 * @property string $created
 * @property string $updated
 * @property integer $deleted
 */
class Storehouse extends ActiveRecord
{
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Storehouse the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors(){
        return array(
            'timestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'updated',
            )
        );
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_storehouse';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('name, storeman_id', 'required'),
            array('name', 'length', 'max'=>50),
            array('description', 'length', 'max'=>255),
            array('user_id, deleted', 'numerical', 'integerOnly'=>true),
		        
	        array('user_id, storeman_id', 'default', 'value' => Yii::app()->user->id, 'on' => 'insert'),
	        array('deleted', 'default', 'value' => 0, 'on' => 'insert'),

    		// The following rule is used by search().
    		// Please remove those attributes that should not be searched.
            array('name, description, storeman_id, user_id, created, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
        return array(
            'stocks' => array(self::HAS_MANY, 'Stock', 'storehouse_id'),
            //'products' => array(self::HAS_MANY, 'Product', array('product_id' => 'id'), 'through' => 'stocks'),
            'products'=>array(self::MANY_MANY, 'Product', 'erp_stock(storehouse_id, product_id)'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'name' => '仓库名称',
			'description' => '描述',
			'storeman_id' => '仓管人',
			'user_id' => '录入人',
			'created' => '录入时间',
			'updated' => '修改时间',
			'deleted' => 'Deleted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('storeman_id',$this->storeman_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getStoreman(){
	    if ($this->storeman_id){
	        return Account::user($this->storeman_id);
	    }
	    return null;
	}
	
    public function newStock($productId, $quantity=0){
        $stock = new Stock();
        $stock->storehouse_id = $this->id;
        $stock->product_id = $productId;
        $stock->quantity = $quantity;
        $stock->save();
        return $stock;
    }
    
    public function addStock($productId, $quantity){
        foreach ($this->stocks as $stock){
            if ($stock->product_id == $productId){
                return $stock->add($quantity);
            }
        }
        $stock = new Stock();
        $stock->storehouse_id = $this->id;
        $stock->product_id = $productId;
        $stock->$quantity = $quantity;
        return $stock->save();
    }
    
    public function subtractStock($productId, $quantity){
        foreach ($this->stocks as $stock){
            if ($stock->product_id == $productId){
                return $stock->subtract($quantity);
            }
        }
    }
    
    public function getDeptName($dept_id){
        return Account::department($dept_id)->name;
    }
    
    protected function beforeDelete(){
        if (parent::beforeDelete()){
            if (count($this->stocks) > 0){
                $this->addError('stocks', '该仓库已有产品库存，不能进行删除操作');
                return false;
            }
        }
        return true;
    }
}
