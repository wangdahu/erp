<?php

/**
 * This is the model class for table "pss_back_sales_item".
 *
 * The followings are the available columns in table 'pss_back_sales_item':
 * @property integer $id
 * @property integer $back_sales_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_no
 * @property string $product_brand
 * @property string $product_unit
 * @property string $product_cate
 * @property integer $quantity
 * @property inteter $storehouse_id
 * @property string $price
 */
class BackSalesItem extends BillFormItem
{
    public function belongId(){
        return 'back_sales_id';
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BackSalesItem the static model class
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
		return 'pss_back_sales_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, product_name, quantity, storehouse_id, quantity, price', 'required'),
			array('back_sales_id, product_id, quantity, storehouse_id, stock_id', 'numerical', 'integerOnly'=>true),
			array('product_name, product_no, product_brand, product_cate', 'length', 'max'=>50),
			array('product_unit', 'length', 'max'=>20),
			array('price', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, back_sales_id, product_id, product_name, product_no, product_brand, product_unit, product_cate, quantity, price', 'safe', 'on'=>'search'),
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
		    'form' => array(self::BELONGS_TO, 'BackSales', 'back_sales_id'),
		    'stock' => array(self::BELONGS_TO, 'Stock', 'stock_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '销售退货明细ID',
			'back_sales_id' => '销售退货ID(外键)',
			'product_id' => '产品ID',
			'product_name' => '产品名称',
			'product_no' => '产品编号',
			'product_brand' => '产品品牌',
			'product_unit' => '产品单位',
			'product_cate' => '产品类别',
			'quantity' => '数量',
			'price' => '价格',
			'storehouse_id' => '所在仓库',
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
		$criteria->compare('back_sales_id',$this->back_sales_id);
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
	
	/**
	 * 验证退货数量
	 * @return bool
	 */
    public function vaildQuantity($form){
        if($form->order_id){
            $sales_order_items = SalesOrderItem::model()->find("product_id=".$this->product_id." and order_id=".$form->order_id);
            if ($this->quantity > $sales_order_items->quantity){
                $this->addError('quantity', '当前退货数量不可超过销售单数量');
                return false;
            }
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
            $stock = new Stock();
            $stock->add($this->quantity);
        }
    }
}