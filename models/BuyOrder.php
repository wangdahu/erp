<?php

/**
 * This is the model class for table "pss_buy_order".
 *
 * The followings are the available columns in table 'pss_buy_order':
 * @property integer $id
 * @property string $no
 * @property integer $user_id
 * @property integer $buyer_id
 * @property string $buyer
 * @property integer $buyer_dept_id
 * @property string $buyer_dept
 * @property string $corp_name
 * @property string $address
 * @property string $linkman
 * @property string $phone
 * @property integer $supplier_id
 * @property string $supplier_name
 * @property string $supplier_address
 * @property string $supplier_linkman
 * @property string $supplier_phone
 * @property string $delivery_date
 * @property string $balance_date
 * @property string $remark
 * @property string $total_price
 * @property integer $status
 * @property integer $is_history
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 * @method BuyOrder incomplete
 * @method BuyOrder history
 * @method BuyOrder approval_pass 通过审批的采购单
 * @property BuyOrderItem[] $items
 */
class BuyOrder extends BillForm
{
    /**
     * @var string Class Name
     */
    protected static $itemModel = 'BuyOrderItem';
    
    /**
     * 采购中
     * @var int
     */
    const STATUS_BUYING = 1;
    /**
     * 入库中
     * @var int
     */
    const STATUS_STORING = 2;
    /**
     * 已入库
     * @var int
     */
    const STATUS_HAS_STORE = 3;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BuyOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
//	public function beforeFind(){
//	    parent::beforeFind();
//	
//	    $viewRights = PssPrivilege::buyCheck(PssPrivilege::BUY_ORDER_VIEW);
//	    if (!$viewRights){
//	        $criteria = new CDbCriteria();
//	        $criteria->compare('buyer_id', Yii::app()->user->id);
//	        $this->getDbCriteria()->mergeWith($criteria);
//	    }
//	}
	
	public function defaultScope(){
        if (!PssPrivilege::buyCheck(PssPrivilege::BUY_ORDER_VIEW)){
            return array(
                'condition' => 'buyer_id=:buyer_id',
                'params' => array(':buyer_id' => Yii::app()->user->id),
            );
        }
        return array();
	}
	
	public function billNumber(){
	    return $this->no;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_buy_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, buyer_id, buyer, buyer_dept_id, buyer_dept, corp_name, address, linkman, phone, supplier_id, supplier_name, supplier_address, supplier_linkman, supplier_phone, delivery_date, balance_date, total_price', 'required'),
			array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('buyer_id, buyer_dept_id, supplier_id, status, approval_id', 'numerical', 'integerOnly'=>true),
			array('no', 'validBillNumber', 'on'=>'insert'),
			array('buyer_id, buyer_dept_id, supplier_id, status, approval_status', 'numerical', 'integerOnly'=>true),
			array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
			array('buyer, buyer_dept, corp_name, linkman, phone, supplier_name, supplier_linkman', 'length', 'max'=>50),
			array('address, supplier_address, supplier_phone', 'length', 'max'=>100),
			array('total_price', 'length', 'max'=>10),
			array('remark, user_id', 'safe'),
	        array('is_history', 'safe', 'on'=>'update'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('no, user_id, buyer_id, total_price, status, date_pattern, start_date, end_date, keyword, approval_id, approval_status', 'safe', 'on'=>'search'),
		);
	}
	
    //public function 

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'items' => array(self::HAS_MANY, self::$itemModel, 'order_id'),
            'products' => array(self::HAS_MANY, 'Product', array('product_id' => 'id'), 'through' => 'items'),
            'inForms' => array(self::HAS_MANY, 'StockIn', 'buy_order_id', 'condition' => 'stockIns.buy_order_id IS NOT NULL'),
            'inItems' =>array(self::HAS_MANY, 'StockInItem', array('id' => 'form_id'), 'through' => 'inForms'),
            'paidPrice' => array(self::STAT, 'PayItem', 'order_id', 'select' => 'SUM(price)'),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
		);
	}
	
	public function getNotPaidPrice(){
	    return $this->total_price-$this->paidPrice;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'no' => '采购单号',
			'user_id' => '录入人id',
			'buyer_id' => '采购员',
		    'buyer_dept_id' => '采购部门ID',
		    'buyer_dept' => '采购部门',
			'buyer' => '采购员',
			'corp_name' => '我方公司地址',
			'address' => '联系地址',
			'linkman' => '我方联系人',
			'phone' => '电话',
			'supplier_id' => '供应商id',
			'supplier_name' => '供应商名称',
			'supplier_address' => '通讯地址',
			'supplier_linkman' => '供应商联系人',
			'supplier_phone' => '电话',
			'delivery_date' => '交付期限',
			'balance_date' => '结算期限',
			'remark' => '备注',
			'total_price' => '总计金额',
			'status' => '采购状态',
			'approval_id' => '审批状态',
			'approval_status' => '审批状态',
			'created' => '填单日期',
			'updated' => '修改时间',
	        'items' => '产品信息',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
    public function search(){
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        
        $criteria=new CDbCriteria;
        if (!empty($this->keyword)){
            $cr=new CDbCriteria;
            $cr->with = array('items');
            $cr->together = true;
            $cr->compare('no', $this->keyword, true, "OR")
               ->compare('supplier_name', $this->keyword, true, "OR")
               ->compare('items.product_name', $this->keyword, true, "OR");
            
            $criteria->mergeWith($cr);
        }
        $criteria->compare('buyer_dept_id', $this->buyer_dept_id);
        $criteria->compare('buyer_id', $this->buyer_id);
        
        if(!empty($this->approval_id)){
            $criteria->join = "left join core_flow_task cft on cft.id=t.approval_id";
            $criteria->condition = "cft.status=".$this->approval_id;
        }
        
        //no, user_id, buyer_id, total_price, status, date_pattern, start_date, end_date
        $criteria->compare('no',$this->no,true, 'or');//弹出层搜索
        $criteria->compare('supplier_name', $this->no, true, 'or');//弹出层搜索
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('buyer_id',$this->buyer_id);
        $criteria->compare('total_price',$this->total_price,true);
        $criteria->compare('t.status',$this->status);
        $criteria->mergeWith($this->compareDate('created'));
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array('pageSize'=>30),
        ));
    }
	
    public function scopes(){
        return array(
            //进行中的采购单
            'incomplete' => array(
                'condition'=> 'is_history=:is_history',
                'params'=> array(':is_history' => 0),
            ),
            //历史采购单
            'history' => array(
                'condition'=> 'is_history=:is_history',
                'params'=> array(':is_history' => 1),
            ),
            //通过审批的采购单
            'hasPass' => array(
                'condition' => 't.approval_status=:approval_status',
                'params' => array(':approval_status' => PssFlow::APPROVAL_PASS),
            ),
        );
    }
    
    public function storeIncomplete(){
    
        $rows = $this->getDbConnection()->createCommand()
            ->select("boi.id, boi.product_id, SUM(boi.quantity) AS quantity, IFNULL(SUM(sii.quantity), 0) AS inQuantity")
            ->from("pss_buy_order_item boi")
            ->join("pss_buy_order bo", "boi.order_id=bo.id")
            ->leftJoin("pss_stock_in si", "bo.id=si.buy_order_id")
            ->leftJoin("pss_stock_item sii", "sii.form_id=si.id AND sii.type=0")
            ->having("quantity>inQuantity")
            ->group("boi.id, boi.product_id")->queryAll();
        $ids = array(0);
        foreach ($rows as $row){
            $ids[] = $row['id'];
        }
        $criteria = new CDbCriteria();
        $criteria->with = 'items';
        $criteria->together = true;
        $criteria->compare('items.id', $ids);
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }
    
    public function intoHistory(){
        $this->setAttribute('is_history', 1);
        return $this->save();
    }
    
    protected function beforeSave(){
        if ($this->isNewRecord){
            $this->user_id = Yii::app()->user->id;
            //$this->status = self::STATUS_BUYING;
        }
        return parent::beforeSave();
    }
	
    protected function afterSave(){
        parent::afterSave();
        
        //把对应进行中的采购单的状态进行调整
        $ids = array();
        foreach ($this->items as $item){
            $ids[] = $item->product_id;
        }
        $criteria = new CDbCriteria();
        $criteria->with = 'items';
        $criteria->compare('items.product_id', $ids);
        foreach (SalesOrder::model()->incomplete()->findAll($criteria) as $salesOrder){
            $salesOrder->updateStatus(SalesOrder::STATUS_BUYING);
        }
    }
    
    public static function getStatusOptions(){
        return array(
            self::STATUS_BUYING => '采购中',
            self::STATUS_STORING => '入库中',
            self::STATUS_HAS_STORE => '已入库',
        );
    }
    
    public function updateStatus($status){
        if ($status > $this->status && array_key_exists($status, self::getStatusOptions())){
            $this->status = $status;
            $this->save();
        }
    }
}