<?php

/**
 * This is the model class for table "pss_stock_allocate_item".
 *
 * The followings are the available columns in table 'pss_stock_allocate_item':
 * @property integer $id
 * @property integer $allocate_id
 * @property integer $from_stock_id
 * @property integer $product_id
 * @property integer $from_storehouse_id
 * @property integer $to_storehouse_id
 * @property string $product_name
 * @property string $product_no
 * @property string $product_brand
 * @property string $product_unit
 * @property string $product_cate
 * @property integer $quantity
 */
class StockAllocateItem extends BillFormItem
{
    public $allocate_man_id;
    public $allocate_dept_id;
    public $cate_id;
    public $created;
    
    public function belongId(){
        return 'allocate_id';
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StockAllocateItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_stock_allocate_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_stock_id, product_id, from_storehouse_id, to_storehouse_id, product_name, quantity', 'required'),
			array('from_stock_id, allocate_id, product_id, from_storehouse_id, to_storehouse_id, quantity', 'numerical', 'integerOnly'=>true),
			array('product_name, product_no, product_brand, product_cate', 'length', 'max'=>50),
			array('product_unit', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, allocate_id, product_id, from_storehouse_id, to_storehouse_id, product_name, product_no, product_brand, product_unit, product_cate, quantity,
			       allocate_man_id, allocate_dept_id, cate_id, created, date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
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
            'allocate' => array(self::BELONGS_TO, 'StockAllocate', 'allocate_id', 'joinType' => 'INNER JOIN'),
            'stock' => array(self::BELONGS_TO, 'Stock', 'from_stock_id'),
            'fromStock' => array(self::BELONGS_TO, 'Stock', 'from_stock_id'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }
    
    public function getToStock(){
        $stock = Stock::model()->find("storehouse_id={$this->to_storehouse_id} and product_id={$this->product_id}");
        if ($stock == null){
            $stock = new Stock();
            $stock->storehouse_id = $this->to_storehouse_id;
            $stock->product_id = $this->product_id;
            $stock->save();
        }
        return $stock;
    }
    
    /**
     * (non-PHPdoc)
     * @see CModel::behaviors()
     */
    public function behaviors(){
        return array(
            'searchAttribute' => array(
                'class' => 'pss.models.behaviors.SearchAttribute',
            ),
        );
    }
    
    /**
     * 获取所有仓库
     */
    public function getStorehouseList(){
        return CHtml::listData(Storehouse::model()->findAll(), 'id', 'name');
    }
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
//	public function beforeValidate(){
//	    if (parent::beforeSave()){
//    	    if($this->quantity<0){
//    	        $this->addError('quantity', '转出数量需大于零！');
//    	        return false;
//    	    }
//    	    if($this->quantity>$this->fromStock->quantity){
//    	        $this->addError('quantity', '转出数量不能大于所在仓库库存！');
//    	        return false;
//    	    }
//    	    return true;
//	    }
//	    return false;
//	}
	
    /**
     * 验证转出数量
     * @return bool
     */
    public function vaildQuantity(){
        if($this->quantity<0){
            $this->addError('quantity', '转出数量需大于零！');
            return false;
        }
        if($this->quantity>$this->fromStock->quantity){
            $this->addError('quantity', '转出数量不能大于所在仓库库存！');
            return false;
        }
        return true;
    }

    protected function afterSave(){
        parent::afterSave();
        if ($this->isNewRecord){
            $this->fromStock->subtract($this->quantity);
            $this->toStock->add($this->quantity);
        }
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'allocate_id' => '调拨单ID',
			'product_id' => '产品ID',
			'from_storehouse_id' => '调拨原仓库',
			'to_storehouse_id' => '调拨目标仓库',
			'product_name' => '产品名称',
			'product_no' => '型号',
			'product_brand' => '品牌',
			'product_unit' => '单位',
			'product_cate' => '产品类别',
			'quantity' => '转出数量',
		    'allocate_man_id' => '调拨人',
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
        $criteria->with = array('allocate', 'product.cate');
        $criteria->together = true;
        if(!empty($this->keyword)){
            $cr = new CDbCriteria();
            $cr->with = array("stock");
            $cr->together = true;
            $cr->compare('product_name', $this->keyword, true, 'or');
            $cr->compare('product_no', $this->keyword, true, 'or');
            $cr->compare('product_brand', $this->keyword, true, 'or');
            $criteria->mergeWith($cr);
        }
        $criteria->compare('from_storehouse_id', $this->from_storehouse_id);
        $criteria->compare('to_storehouse_id', $this->to_storehouse_id);
        $criteria->compare('allocate.allocate_id', $this->allocate_man_id);
        $criteria->compare('allocate.allocate_dept_id', $this->allocate_dept_id);
        $criteria->compare('cate.id', $this->cate_id);
        $criteria->mergeWith($this->compareDate('allocate.created'));
        
        return new CActiveDataProvider($this, array(
        	'criteria'=>$criteria,
            'pagination' => array('pageSize' => 30),
        ));
    }
}