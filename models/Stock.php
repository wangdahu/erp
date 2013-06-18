<?php

/**
 * This is the model class for table "erp_stock".
 *
 * The followings are the available columns in table 'erp_stock':
 * @property integer $id
 * @property integer $product_id
 * @property integer $storehouse_id
 * @property integer $quantity
 * @property integer $no;
 */
class Stock extends ActiveRecord
{
    public $storeman_id;
    public $cate_id;
    
    public function beforeFind(){
        parent::beforeFind();
    
/*        $viewRights = ErpPrivilege::stockCheck(ErpPrivilege::STOCK_VIEW) || ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD);
        if (!$viewRights){
            $this->getDbCriteria()->condition = "1=0";
        }*/
    }
    
    /**
     * (non-PHPdoc)
     * @see CModel::behaviors()
     */
    public function behaviors(){
        return array(
            'searchAttribute' => array(
                'class' => 'erp.models.behaviors.SearchAttribute',
            ),
        );
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StockPile the static model class
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
		return 'erp_stock';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, storehouse_id', 'required'),
			array('product_id, storehouse_id, quantity', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, product_id, storehouse_id, quantity, no, cate_id,
			       dept_id, storeman_id, date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
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
            'storehouse' => array(self::BELONGS_TO, 'Storehouse', 'storehouse_id'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            /*'bideInQuantity' => array(self::BELONGS_TO, 'StockInItem', 'stock_id', 
                'with' => array('form' => array('condition' => 'form.approval_status=:status', 'params' => array(':status' => ErpFlow::APPROVAL_FOLLOW)))),*/
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'product_id' => '产品',
		    'no' => '调拨单号',
			'storehouse_id' => '仓库',
			'quantity' => '当前库存',
		    'storeman_id' => '仓库管理员',
		);
	}
    
    /**
     * 获得待入库数量
     */
    public function getBideInQuantity(){
        $criteria = new CDbCriteria();
        $criteria->select = 'quantity';
        $criteria->with = array(
            'form' => array(
                'condition' => 'form.approval_status=:status', 
                'params' => array(':status' => ErpFlow::APPROVAL_FOLLOW),
                'select' => 'form.id',
            )
        );
        $criteria->condition = 'stock_id='.$this->id;
        $models = StockInItem::model()->findAll($criteria);
        $quantity = 0;
        foreach ($models as $model) {
            $quantity += $model->quantity;
        }
        return $quantity;
    }
    
    /**
     * 获得待出库数量
     */
    public function getBideOutQuantity(){
        $criteria = new CDbCriteria();
        $criteria->select = 'quantity';
        $criteria->with = array(
            'form' => array(
                'condition' => 'form.approval_status=:status',
                'params' => array(':status' => ErpFlow::APPROVAL_FOLLOW),
                'select' => 'form.id',
            ),
        );
        $criteria->condition = 'stock_id='.$this->id;
        $models = StockOutItem::model()->findAll($criteria);
        $quantity = 0;
        foreach ($models as $model){
            $quantity += $model->quantity;
        }
        return $quantity;
    }
    
    /**
     * 获取所有仓库
     */
    public function getStorehouseList(){
        return CHtml::listData(Storehouse::model()->findAll(), 'id', 'name');
    }
    
    /**
     * 获取所有产品类别
     */
    public function getCateList(){
        return CHtml::listData(ProductCate::model()->findAll(), 'id', 'name');
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
        $criteria->with = array('product', 'storehouse');
        if(!empty($this->keyword)){
            $criteria->with = array('product' => array('with' => 'brand'));
            $criteria->together = true;
            $criteria->compare('product.name', $this->keyword, true, 'or')
                    ->compare('product.no', $this->keyword, true, 'or')
                    ->compare('brand.name', $this->keyword, true, 'or');
        }
        if(!empty($this->dept_id)){
            $users = Account::departmentUsers($this->dept_id);
            $ids = array_keys($users);
            $ids[] = 0;
            $criteria->compare('storehouse.storeman_id', $ids);
        }
        $criteria->compare('storehouse.storeman_id', $this->storeman_id);
        $criteria->compare('storehouse_id',$this->storehouse_id);
        $criteria->compare('product.cate_id', $this->cate_id);
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30,
            ),
        ));
    }
    
    public function updateStock($productId, $quantity){
        $stockPile = Stock::model()->find("storehouse_id={$this->id} and product_id={$productId}");
        $stockPile = $productId;
        $stockPile->save();
    }
    
    public function initStock(Storehouse $house, Product $product, $quantity){
        $this->quantity = $quantity;
        return $this->save();
    }
    
    public function getIsEmpty(){
        return $this->quantity == 0;
    }
    
    public function add($quantity){
        $this->quantity += $quantity;
        return $this->save();
    }
    
    public function subtract($quantity){
        $this->quantity -= $quantity;
        return $this->save();
    }
}
