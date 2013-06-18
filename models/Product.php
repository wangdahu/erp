<?php

/**
 * This is the model class for table "pss_product".
 *
 * The followings are the available columns in table 'pss_product':
 * @property integer $id
 * @property string $name
 * @property integer $cate_id
 * @property string $unit_id
 * @property string $brand_id
 * @property string $no
 * @property string $code
 * @property string $jan_code
 * @property integer $user_id
 * @property integer $safe_quantity
 * @property integer $min_quantity
 * @property string $producting_place
 * @property string $buy_price
 * @property string $sale_price
 * @property string $discount_price
 * @property string $wholesale_price
 * @property string $low_price
 * @property string $cost
 * @property string $photo
 * @property string $remark
 * @property string $created
 * @property string $updated
 * @property integer $deleted
 * @property ProductDetail $detail
 * @property ProductCate $cate
 * @property ProductUnit $unit
 * @property ProductBrand $brand
 */
class Product extends ActiveRecord
{
    public $buyer_id;
    
    public function beforeFind(){
        parent::beforeFind();
    
/*        $viewRights = PssPrivilege::stockCheck(PssPrivilege::STOCK_VIEW) || PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD);
        if (!$viewRights){
            $this->getDbCriteria()->condition = "1=0";
        }*/
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Product the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
    public function init(){
        parent::init();
        if ($this->isNewRecord){
            $this->detail = new ProductDetail;
        }
    }

    public function defaultScope(){
        return array(
            //'with' => array('cate', 'unit', 'brand'),
        );
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
		return 'pss_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, cate_id, unit_id, no', 'required'),
		    
			array('cate_id, safe_quantity, min_quantity', 'numerical', 'integerOnly'=>true),
			array('buy_price, sales_price, discount_price, wholesales_price, low_price, cost', 'numerical'),
			array('name, brand_id, no, producting_place', 'length', 'max'=>50),
			array('code, jan_code', 'length', 'max'=>100),
			array('remark', 'safe'),
	        array('user_id', 'default', 'value' => Yii::app()->user->id, 'on' => 'insert'),
	        array('deleted', 'default', 'value' => 0, 'on' => 'insert'),
		        
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, cate_id, unit_id, brand_id, no, code, jan_code, user_id, safe_quantity, min_quantity, producting_place, buy_price, 
			       sale_price, discount_price, wholesale_price, low_price, cost, photo, remark, created,
			       dept_id, buyer_id, date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
		);
	}

    /**
     * @return array relational rules.
     */
    public function relations(){
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'detail' => array(self::HAS_ONE, 'ProductDetail', 'product_id'),
            'cate' => array(self::BELONGS_TO, 'ProductCate', 'cate_id', 'select'=>array('id', 'name')),
            'unit' => array(self::BELONGS_TO, 'ProductUnit', 'unit_id', 'select'=>array('id', 'name')),
            'brand' => array(self::BELONGS_TO, 'ProductBrand', 'brand_id', 'select'=>array('id', 'name')),
            'stocks' => array(self::HAS_MANY, 'Stock', 'product_id'),
            'storehouses' => array(self::HAS_MANY, 'Storehouse', array('storehouse_id'=>'id'), 'through'=>'stocks'),
            'totalStock' => array(self::STAT, 'Stock', 'product_id', 'select'=>'SUM(quantity)'),
            'salesItems' => array(self::HAS_MANY, 'SalesOrderItem', 'product_id'),
            'salesQuantity' => array(self::STAT, 'SalesOrderItem', 'product_id', 'select'=>'SUM(quantity)', 
                'join' => 'INNER JOIN pss_sales_order form ON form.id=order_id', 'condition' => 'form.approval_status='.PssFlow::APPROVAL_PASS),
            'preSalesQuantity' => array(self::STAT, 'SalesOrderItem', 'product_id', 'select'=>'SUM(quantity)', 
                'join' => 'INNER JOIN pss_sales_order form ON form.id=order_id', 'condition' => 'form.is_history=0 and form.approval_status='.PssFlow::APPROVAL_PASS),
            'salesOrderCount' => array(self::STAT, 'SalesOrderItem', 'product_id'),
            'salesTotalPrice' => array(self::STAT, 'SalesOrderItem', 'product_id', 'select'=>'SUM(price)'),
            'buyItems' => array(self::HAS_MANY, 'BuyOrderItem', 'product_id'),
            'buyOrderCount' => array(self::STAT, 'BuyOrderItem', 'product_id'),
            'buyTotalPrice' => array(self::STAT, 'BuyOrderItem', 'product_id', 'select'=>'SUM(price)'),
            //使用getBuyQuantity()，因为需求上有需要所有采购数量，也有采购中的数量
            'buyQuantity' => array(self::STAT, 'BuyOrderItem', 'product_id', 'select'=>'SUM(quantity)'),
            'buyAssignments' => array(self::HAS_MANY, 'BuyAssignment', 'product_id'),
        );
    }
    
    public function getBuyerText(){
        $buyers = array();
        foreach ($this->buyAssignments as $assigment){
            if ($assigment->type == BuyAssignment::TYPE_ROLE){
                $buyer = PssRole::model()->find($assigment->assign_id);
            }else{
                $buyer = PssUser::model()->find($assigment->assign_id);
            }
            if ($buyer != null){
                $buyers[] = $buyer->name;
            }
        }
        return implode('，', $buyers);
    }

    
    public function getUrgedCount(){
        $count = 0;
        foreach ($this->salesItems as $item){
            $count += $item->urgedCount;
        }
        return $count;
    }
    
    /**
     * @return array customized attribute labels (name=>label)
        +------------------+---------------+------+-----+---------+----------------+
        | Field            | Type          | Null | Key | Default | Extra          |
        +------------------+---------------+------+-----+---------+----------------+
        | id               | int(11)       | NO   | PRI | NULL    | auto_increment |
        | name             | varchar(50)   | NO   |     | NULL    |                |
        | cate_id          | int(11)       | NO   |     | NULL    |                |
        | unit_id          | varchar(20)   | NO   |     |         |                |
        | brand_id         | varchar(50)   | YES  |     |         |                |
        | no               | varchar(50)   | NO   |     | NULL    |                |
        | code             | varchar(100)  | YES  |     | NULL    |                |
        | jan_code         | varchar(100)  | YES  |     | NULL    |                |
        | user_id          | int(11)       | NO   |     | NULL    |                |
        | safe_quantity    | int(11)       | YES  |     | NULL    |                |
        | min_quantity     | int(11)       | YES  |     | NULL    |                |
        | producting_place | varchar(50)   | YES  |     |         |                |
        | buy_price        | decimal(10,2) | YES  |     | NULL    |                |
        | sales_price      | decimal(10,2) | YES  |     | NULL    |                |
        | discount_price   | decimal(10,2) | YES  |     | NULL    |                |
        | wholesales_price  | decimal(10,2) | YES  |     | NULL    |                |
        | low_price        | decimal(10,2) | YES  |     | NULL    |                |
        | cost             | decimal(10,2) | YES  |     | NULL    |                |
        | photo            | varchar(255)  | YES  |     |         |                |
        | remark           | text          | YES  |     | NULL    |                |
        | created          | datetime      | NO   |     | NULL    |                |
        | updated          | datetime      | NO   |     | NULL    |                |
        | deleted          | tinyint(4)    | NO   |     | 0       |                |
        +------------------+---------------+------+-----+---------+----------------+
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'name' => '产品名称',
			'cate_id' => '产品分类',
			'unit_id' => '产品单位',
			'brand_id' => '产品品牌',
			'no' => '产品型号',
			'code' => '产品编码',
			'jan_code' => '产品条码',
			'user_id' => '录入人',
			'safe_quantity' => '库存报警',
			'min_quantity' => '最少订量',
			'producting_place' => '生产产地',
			'buy_price' => '采购价',
			'sales_price' => '销售价',
			'discount_price' => '折扣价',
			'wholesales_price' => '批发价',
			'low_price' => '销售底价',
			'cost' => '参考成本',
			'photo' => '产品图片',
			'remark' => '产品备注',
			'created' => '创建时间',
			'updated' => '修改时间',
			'deleted' => '删除标志',
			'totalStock' => '当前库存',
			'salesQuantity' => '预销售量',
		    'buyer_id' => '采购负责人',
		);
	}
    
    protected function afterSave(){
        parent::afterSave();
        if ($this->isNewRecord){
            $this->detail->product_id = $this->id;
        }
        $this->detail->save();
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
        if (!empty($this->keyword)){
            $cr=new CDbCriteria;
            $cr->with = array('brand',);
            $cr->together = true;
            $cr->compare('t.name', $this->keyword, true, "OR")
               ->compare('t.no', $this->keyword, true, "OR")
               ->compare('brand.name', $this->keyword, true, "OR");
            $criteria->mergeWith($cr);
        }
        
        if (!empty($this->dept_id)){
            $cr2 = new CDbCriteria();
            $cr2->with = 'buyAssignments';
            $cr2->together = true;
            $ids = array_keys(Account::departmentUsers($this->dept_id));
            $ids[] = 0; //防止部门下面没员工
            $cr2->compare('buyAssignments.assign_id', $ids);
            $cr2->compare('buyAssignments.type', BuyAssignment::TYPE_USER);
            $criteria->mergeWith($cr2);
        }
        
        if (!empty($this->buyer_id)){
            $cr3 = new CDbCriteria();
            $cr3->with = 'buyAssignments';
            $cr3->together = true;
            //PssUser::model()->getBuyAssignments();
            //PssRole::model()->getBuyAssignments();
            $cr3->compare('buyAssignments.assign_id', $this->buyer_id);
            $cr3->compare('buyAssignments.type', BuyAssignment::TYPE_USER);
            $criteria->mergeWith($cr3);
        }
        
        $criteria->mergeWith($this->compareDate('t.created'));
        
        
        $criteria->compare('t.id',$this->id);
        $criteria->compare('t.name',$this->name,true);
        $criteria->compare('t.cate_id',$this->cate_id,false);
        $criteria->compare('t.brand_id',$this->brand_id,false);
        $criteria->compare('t.no',$this->no,true);
        $criteria->compare('t.code',$this->code,true);
        $criteria->compare('t.jan_code',$this->jan_code,true);
        $criteria->with = array('cate', 'unit', 'brand', 'totalStock');
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria, 
            'pagination'=>array(
                'pageSize'=>30,
            ),
        ));
	}
	
	/**
	 * 缺货的命名范围
	 * @return Product
	 */
	public function stockout(){
	    
	    /* $this->getDbCriteria()->mergeWith(array('condition'=>));
	    $this->totalStock;0
	    $this->safe_quantity; */
	    $rows = $this->getDbConnection()->createCommand()
	        ->select("pp.id, SUM(ps.quantity) AS totalStock, SUM(psi.quantity) AS salesQuantity, pp.safe_quantity")
	        ->from("pss_product pp")
	        ->leftJoin("pss_sales_order_item psi", "pp.id=psi.product_id")
	        ->leftJoin("pss_stock ps", "pp.id=ps.product_id")
	        ->join("pss_sales_order pso", "psi.order_id=pso.id")
	        ->where("pso.approval_status=".PssFlow::APPROVAL_PASS." and pso.is_history=0")
	        ->having("pp.safe_quantity>totalStock or pp.safe_quantity is null")
	        ->group("pp.id")->queryAll();
	    $ids = array(0);
	    /* $products = $this->populateRecords($rows, true);
	    foreach ($products as $product){
	        if ($product->getIsStockout()){
	            $ids[] = $product->id;
	        }
	    } */
	    foreach ($rows as $row){
	        $stockout = (int)$row['totalStock'] < (int)$row['safe_quantity'] 
	            || (int)($row['totalStock'] - $row['salesQuantity']) < (int)$row['safe_quantity'];
	        if ($stockout){
	            $ids[] = $row['id'];
	        }
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare('t.id', $ids);
	    //$criteria->with = array('salesItems');
	    //$criteria->with = array('salesQuantity', 'buyItems' => array('condition'));
/* 	    $criteria->with = array('salesItems' => array(
            'join' => sprintf("INNER JOIN %s as form ON form.id=order_id", SalesOrder::model()->tableName()),
            'condition'=>'form.status<>'.SalesOrder::STATUS_DELIVERED.' AND is_history=0')); */
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
    public function assignTo($uid){
            $productIds = array();
            foreach (PssUser::model()->find($uid)->getBuyAssignments() as $assginment){
                $productIds[] = $assginment->product_id;
            }
            $roleIds = Account::userRoles($uid);
            $roles = PssRole::model()->findAll();
            foreach (PssRole::model()->findAll() as $role){
                if (!in_array($role->id, $roleIds)) continue;
                foreach($role->getBuyAssignments() as $assginment){
                    $productIds[] = $assginment->product_id;
                }
            }
            $criteria = new CDbCriteria();
            if ($productIds){
                $criteria->compare('t.id', $productIds);
            }else{
                $criteria->condition = '1=0';
            }
            $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }
	
    public function scopes(){
        return array(
            'hasSalesOrder' => array(
                'with' => array('salesOrderCount', 'salesTotalPrice', 'salesQuantity', 'buyOrderCount', 'buyTotalPrice', 'buyQuantity'),
                'condition' => 'exists(select * from pss_sales_order_item where product_id=t.id)',
            )
        );
    }
	
	public function getIsStockout(){
	    return $this->totalStock < $this->safe_quantity
	        || ($this->totalStock - $this->salesQuantity) < $this->safe_quantity;
	}
	
	public static function getUnitListData(){
	    return CHtml::listData(ProductUnit::model()->findAll(), 'id', 'name');
	}
	
	public static function getCateListData(){
	    return CHtml::listData(ProductCate::model()->findAll(), 'id', 'name');
	}
	
	public static function getBrandListData(){
	    return CHtml::listData(ProductBrand::model()->findAll(), 'id', 'name');
	}
}