<?php

/**
 * This is the model class for table "pss_sales_order_detail".
 *
 * The followings are the available columns in table 'pss_sales_order_detail':
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_no
 * @property string $product_brand
 * @property string $product_unit
 * @property string $product_cate
 * @property integer $quantity
 * @property string $price
 * @method history
 * @method hasPass
 */
class SalesOrderItem extends BillFormItem
{
    public function belongId(){
        return 'order_id';
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SalesOrderDetail the static model class
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
		return 'pss_sales_order_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, product_name, quantity, price', 'required'),
			array('quantity', 'numerical', 'integerOnly'=>true),
			array('product_name, product_no, product_brand, product_cate', 'length', 'max'=>50),
			array('product_unit', 'length', 'max'=>20),
			array('price', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, order_id, product_id, product_name, product_no, product_brand, product_unit, product_cate, quantity, price', 'safe', 'on'=>'search'),
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
	        'form' => array(self::BELONGS_TO, 'SalesOrder', 'order_id', 'joinType' => 'INNER JOIN'),
	        'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
	        'urgeds' => array(self::HAS_MANY, 'BuyUrged', 'item_id'),
	        'urgedCount' => array(self::STAT, 'BuyUrged', 'item_id'),
	        //'stock' => array(self::BELONGS_TO, 'SalesOrder', 'order_id'),
	        //'totalStock' => array(self::STAT, 'Stock', 'product_id', 'select'=>'SUM(quantity)'),
		);
	}
	
	public function getOutQuantity(){
	    $quantity = 0;
	    foreach ($this->form->outItems as $item){
	        if ($item->product_id == $this->product_id){
	            $quantity += $item->quantity;
	        }
	    }
	    return $quantity;
	}
	
	public function scopes(){
        return array(
/*            'incomplete' => array(
                'with'=>'form',
                'select'=> '',
                'condition'=>'form.status<>:status',
                'params'=> array(':status' => SalesOrder::STATUS_DELIVERED),
            ),*/
        );
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'order_id' => '所属销售单',
			'product_id' => '产品id',
			'product_name' => '产品名称',
			'product_no' => '型号',
			'product_brand' => '品牌',
			'product_unit' => '单位',
			'product_cate' => '类别',
			'quantity' => '数量',
			'price' => '单价',
			'totalPrice' => '总价',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('product_name',$this->product_name,true);
		$criteria->compare('product_no',$this->product_no,true);
		$criteria->compare('product_brand',$this->product_brand,true);
		$criteria->compare('product_unit',$this->product_unit,true);
		$criteria->compare('product_cate',$this->product_cate,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('price',$this->price,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}