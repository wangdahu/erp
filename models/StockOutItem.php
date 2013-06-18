<?php

/**
 * This is the model class for table "erp_stock_item".
 *
 * The followings are the available columns in table 'erp_stock_item':
 * @property integer $id
 * @property integer $stock_id
 * @property integer $type
 * @property integer $product_id
 * @property integer $storehouse_id
 * @property string $product_name
 * @property string $product_no
 * @property string $product_brand
 * @property string $product_unit
 * @property string $product_cate
 * @property integer $quantity
 * @property string $price
 * @property Stock stock
 */
class StockOutItem extends BillFormItem
{
    
    public $out_id;
    public $approval_id;
    public $out_dept_id;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StockOutItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
    public function defaultScope(){
        return array(
            'condition' => 'type=1',
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
		return 'erp_stock_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
	        array('type', 'default', 'value'=>1),
			array('type, product_id, storehouse_id, product_name, quantity, price', 'required'),
			array('stock_id, form_id, type, product_id, storehouse_id, quantity', 'numerical', 'integerOnly'=>true),
			array('product_name, product_no, product_brand, product_cate', 'length', 'max'=>50),
			array('product_unit', 'length', 'max'=>20),
			array('price', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, stock_id, type, product_id, storehouse_id, product_name, product_no, product_brand, product_unit, product_cate, quantity, price,
			       date_pattern, start_date, end_date, keyword, out_id, out_dept_id, approval_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.       'condition' => 'type=1'
        return array(
            'form' => array(self::BELONGS_TO, 'StockOut', 'form_id', 'joinType'=>'INNER JOIN'),
            'stock' => array(self::BELONGS_TO, 'Stock', 'stock_id'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            'storehouse' => array(self::BELONGS_TO, 'Storehouse', 'storehouse_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '序号',
			'stock_id' => '所属出库单',
			'type' => 'Type',
			'product_id' => '所属产品',
			'storehouse_id' => '所在仓库',
			'product_name' => '产品名称',
			'product_no' => '产品编号',
			'product_brand' => '品牌',
			'product_unit' => '单位',
			'product_cate' => '类型',
			'quantity' => '数量',
			'price' => '销售价',
			'out_id' => '出库人',
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
		    $cr->compare("product_name", $this->keyword, true, "or");
		    $cr->compare("form.customer_name", $this->keyword, true, "or");
		    $cr->compare("form.no", $this->keyword, true, "or");
		    $criteria->mergeWith($cr);
		}
		if(!empty($this->approval_id)){
		    $criteria->with = array('form'=>array('join'=>"LEFT JOIN core_flow_task cft ON cft.id=form.approval_id"));
		    $criteria->condition = "cft.status=".$this->approval_id;
		}
		$criteria->mergeWith($this->compareDate('form.created'));
		$criteria->compare('form.out_id', $this->out_id);
		$criteria->compare('form.out_dept_id', $this->out_dept_id);
		return new CActiveDataProvider($this, array(
		    'criteria'=>$criteria,
		    'pagination'=>array('pageSize'=>30),
		));
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
	
	protected function beforeSave(){
	    if (parent::beforeSave() && $this->isNewRecord){
	        $stock = Stock::model()->find("storehouse_id={$this->storehouse_id} and product_id={$this->product_id}");
	        $this->stock_id = $stock->id;
	        return true;
	    }
	    return false;
	}
	
	/**
	 * 验证出库数量
	 * @return bool
	 */
	public function vaildQuantity(StockOut $form){
	    if (!$stock = Stock::model()->find("storehouse_id={$this->storehouse_id} and product_id={$this->product_id}")){
	        $this->addError('quantity', '出库数量不可超过所在仓库库存数目');
	        return false;
	    }
	    if ($this->quantity > $stock->quantity){
	        $this->addError('quantity', '出库数量不可超过所在仓库库存数目');
	        return false;
	    }
	    if ($form->isBindOrder){
	        $salesOrder = SalesOrder::model()->findByPk($form->sales_order_id);
    	    foreach ($salesOrder->items as $item){
    	        if ($item->product_id == $this->product_id && ($this->quantity + $item->outQuantity) > $item->quantity){
    	            $this->addError('quantity', '出库总数量不允许超过关联销售单中未出库的数量');
    	            return false;
    	        }
    	    }
	    }
	    return true;
	}
	
    protected function afterSave(){
        parent::afterSave();
        if ($this->isNewRecord){
            $this->stock->subtract($this->quantity);
        }
    }
}
