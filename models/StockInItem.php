<?php

/**
 * This is the model class for table "pss_stock_item".
 *
 * The followings are the available columns in table 'pss_stock_item':
 * @property integer $id
 * @property integer $form_id
 * @property integer $type
 * @property integer $stock_id
 * @property integer $product_id
 * @property integer $storehouse_id
 * @property string $product_name
 * @property string $product_no
 * @property string $product_brand
 * @property string $product_unit
 * @property string $product_cate
 * @property integer $quantity
 * @property string $price
 */
class StockInItem extends BillFormItem
{
    public $in_id;
    public $approval_id;
    public $in_dept_id;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StockInItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	
    public function defaultScope(){
        return array(
            'condition' => 'type=0',
        );
    }
	
	public function belongId(){
	    return 'form_id';
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_stock_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
	        array('type', 'default', 'value'=>0),
			array('type, product_id, storehouse_id, product_name, quantity, price', 'required'),
			array('stock_id, product_id, storehouse_id, quantity', 'numerical', 'integerOnly'=>true),
			array('product_name, product_no, product_brand, product_cate', 'length', 'max'=>50),
			array('product_unit', 'length', 'max'=>20),
			array('price', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, stock_id, type, product_id, storehouse_id, product_name, product_no, product_brand, product_unit, product_cate, quantity, price,
			        date_pattern, start_date, end_date, keyword, in_id, approval_id, in_dept_id', 'safe', 'on'=>'search'),
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
            'form' => array(self::BELONGS_TO, 'StockIn', 'form_id', 'joinType'=>'INNER JOIN'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            'storehouse' => array(self::BELONGS_TO, 'Storehouse', 'storehouse_id'),
            'stock' => array(self::BELONGS_TO, 'Stock', 'stock_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'form_id' => '所属入库单',
			'stock_id' => 'Stock',
			'type' => 'Type',
			'product_id' => '所属产品',
			'storehouse_id' => '所在仓库',
			'product_name' => '产品名称',
			'product_no' => '型号',
			'product_brand' => '品牌',
			'product_unit' => '单位',
			'product_cate' => '类别',
			'quantity' => '数量',
			'price' => '单价',
			'in_id' => '入库人',
			'approval_id' => '审批状态',
		);
	}

    public function getTotalPrice(){
        return sprintf("%.2f", $this->price*$this->quantity);
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
        $criteria->with = array('form');
        $criteria->together = true;
        
        if(!empty($this->keyword)){
            $cr = new CDbCriteria();
            $cr->compare('product_name', $this->keyword, true, 'or');
            $cr->compare('form.no', $this->keyword, true, 'or');
            $cr->compare('form.supplier_name', $this->keyword, true, 'or');
            $criteria->mergeWith($cr);
        }
        
        $criteria->mergeWith($this->compareDate('form.created'));
        $criteria->compare('form.in_id',$this->in_id);
        $criteria->compare('form.in_dept_id',$this->in_dept_id);
        $criteria->compare('stock_id',$this->stock_id);
        if(!empty($this->approval_id)){
            $criteria->with = array('form' => array('join' => "LEFT JOIN core_flow_task cft ON cft.id=form.approval_id"));
            $criteria->condition = "cft.status=".$this->approval_id;
        }
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),
        ));
    }
    
    /**
     * 验证入库数量
     * @return bool
     */
    public function vaildQuantity(StockIn $form){
        if($form->isBindOrder){
            $buyOrder = BuyOrder::model()->findByPk($form->buy_order_id);
            foreach ($buyOrder->items as $item){
                if($item->product_id == $this->product_id && ($item->inQuantity+$this->quantity) > $item->quantity){
                    $this->addError('quantity', '入库总数量不允许超过关联采购单中未入库的数量');
    	            return false;
                }
            }
        }
        return true;
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
	
    protected function beforeSave(){
        if (parent::beforeSave() && $this->isNewRecord){
            if (!$stock = Stock::model()->find("storehouse_id={$this->storehouse_id} and product_id={$this->product_id}")){
                $stock = $this->createStock();
            }
            $this->stock_id = $stock->id;
            return true;
        }
        return false;
    }
    
    protected function afterSave(){
        parent::afterSave();
        if ($this->isNewRecord){
            $this->stock->add($this->quantity);
        }
    }
    
    protected function createStock(){
        $stock = new Stock();
        $stock->storehouse_id = $this->storehouse_id;
        $stock->product_id = $this->product_id;
        $stock->save();
        return $stock;
    }
    
}