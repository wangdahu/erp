<?php

/**
 * This is the model class for table "pss_back_buy_item".
 *
 * The followings are the available columns in table 'pss_back_buy_item':
 * @property integer $id
 * @property integer $back_buy_id
 * @property integer $product_id
 * @property integer $storehouse_id
 * @property integer $stock_id
 * @property string $product_name
 * @property string $product_no
 * @property string $product_brand
 * @property string $product_unit
 * @property string $product_cate
 * @property integer $quantity
 * @property string $price
 */
class BackBuyItem extends BillFormItem
{
    
    public function belongId(){
        return 'back_buy_id';
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BackBuyItem the static model class
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
		return 'pss_back_buy_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('storehouse_id, product_id, product_name, quantity, price', 'required'),
			array('back_buy_id, stock_id, storehouse_id, product_id, quantity', 'numerical', 'integerOnly'=>true),
			array('product_name, product_no, product_brand, product_cate', 'length', 'max'=>50),
			array('product_unit', 'length', 'max'=>20),
			array('price', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, back_buy_id, product_id, product_name, product_no, product_brand, product_unit, product_cate, quantity, price', 'safe', 'on'=>'search'),
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
	        'form' => array(self::BELONGS_TO, 'BackBuy', 'back_buy_id'),
			'stock' => array(self::BELONGS_TO, 'Stock', 'stock_id'),
		);
	}
	
	public function getTotalPrice(){
	    return sprintf("%.2f", $this->price*$this->quantity);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'back_buy_id' => '采购退货编号',
			'product_id' => '产品id',
	        'stock_id' => '库存', 
	        'storehouse_id' => '仓库',
			'product_name' => '产品名称',
			'product_no' => '产片编号',
			'product_brand' => '品牌',
			'product_unit' => '单位',
			'product_cate' => '类别',
			'quantity' => '数量',
			'price' => '单价',
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

		$criteria->compare('id', $this->id);
		$criteria->compare('back_buy_id', $this->back_buy_id);
		$criteria->compare('product_id', $this->product_id);
		$criteria->compare('product_name', $this->product_name, true);
		$criteria->compare('product_no', $this->product_no, true);
		$criteria->compare('product_brand', $this->product_brand, true);
		$criteria->compare('product_unit', $this->product_unit, true);
		$criteria->compare('product_cate', $this->product_cate, true);
		$criteria->compare('quantity', $this->quantity);
		$criteria->compare('price', $this->price, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 验证退货数量
	 * @return bool
	 */
    public function vaildQuantity(){
        if (!$stock = Stock::model()->find("storehouse_id={$this->storehouse_id} and product_id={$this->product_id}")){
            $this->addError('quantity', '退货数量不可超过当前库存数目');
            return false;
        }
        if ($this->quantity > $stock->quantity){
            $this->addError('quantity', '退货数量不可超过当前库存数目');
            return false;
        }
        return true;
	}
    	
    protected function beforeSave(){
        if (parent::beforeSave() && $this->isNewRecord){
            $stock = Stock::model()->find("storehouse_id={$this->storehouse_id} and product_id={$this->product_id}");
            $this->stock_id = $stock->id;
            return true;
        }
        return false;
    }
    
    protected function afterSave(){
        parent::afterSave();
        if ($this->isNewRecord){
            $this->stock->subtract($this->quantity);
        }
    }
}