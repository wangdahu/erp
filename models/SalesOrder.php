<?php

/**
 * This is the model class for table "pss_sales_order".
 *
 * The followings are the available columns in table 'pss_sales_order':
 * @property integer $id
 * @property string $no
 * @property integer $user_id
 * @property integer $salesman_id
 * @property string $salesman
 * @property integer $salesman_dept_id
 * @property string $salesman_dept
 * @property string $corp_name
 * @property string $address
 * @property string $linkman
 * @property string $phone
 * @property integer $customer_id
 * @property string $customer_name
 * @property string $customer_address
 * @property string $customer_linkman
 * @property string $customer_phone
 * @property string $delivery_date
 * @property string $balance_date
 * @property string $remark
 * @property string $total_price
 * @property integer $status
 * @property integer $is_history
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 * @property SalesOrderItem[] $items
 * @method SalesOrder incomplete
 * @method SalesOrder hasPass
 * @method SalesOrder history
 */
class SalesOrder extends BillForm
{
    /**
     * @var string Class Name
     */
    protected static $itemModel = 'SalesOrderItem';
    
    /**
     * 初始
     * @var int
     */
    const STATUS_INIT = 1;
    /**
     * 采购中
     * @var int
     */
    const STATUS_BUYING = 2;
    /**
     * 发货中
     * @var int
     */
    const STATUS_DELIVERING = 3;
    /**
     * 已发货
     * @var int
     */
    const STATUS_DELIVERED = 4;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SalesOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
    public function beforeFind(){
        parent::beforeFind();
        $viewRights = PssPrivilege::salesCheck(PssPrivilege::SALES_ORDER_VIEW);
        if (!$viewRights){
            $criteria = new CDbCriteria();
            $criteria->compare('salesman_id', Yii::app()->user->id);
            $this->getDbCriteria()->mergeWith($criteria);
        }
    }
    
    public function defaultScope(){
        $viewRights = PssPrivilege::salesCheck(PssPrivilege::SALES_ORDER_VIEW);
        if (!$viewRights){
            return array(
                'condition' => 'salesman_id=:salesman_id',
                'params' => array(':salesman_id' => Yii::app()->user->id),
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
		return 'pss_sales_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, salesman_id, salesman, salesman_dept_id, salesman_dept, corp_name, address, linkman, phone, customer_id, customer_name, customer_address, customer_linkman, customer_phone, delivery_date, balance_date, total_price', 'required'),
	        array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('no', 'validBillNumber', 'on'=>'insert'),
	        array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
	        //array('no', 'validBillNumber', 'on'=>'insert'),
	        array('salesman_id, salesman_dept_id, customer_id, status', 'numerical', 'integerOnly'=>true),
        	array('no, salesman, corp_name, salesman_dept, linkman, phone, customer_name, customer_linkman', 'length', 'max'=>50),
        	array('address, customer_address, customer_phone', 'length', 'max'=>100),
        	array('remark, user_id', 'safe'),
        	array('is_history', 'safe', 'on'=>'update'),
        	// The following rule is used by search().
        	// Please remove those attributes that should not be searched.
        	array('salesman_dept_id, salesman_id, status, date_pattern, start_date, end_date, keyword,
        	        approval_id, created', 'safe', 'on'=>'search'),
        );
	}
    
    protected function beforeSave(){
        if ($this->isNewRecord){
            $this->user_id = Yii::app()->user->id;
            //$this->status = self::STATUS_INIT;
        }
        return parent::beforeSave();
    }

    
    public function intoHistory(){
        $this->setAttribute('is_history', 1);
        return $this->save();
    }
    
    public function allSalesManId(){
        $command = $this->getDbConnection()->createCommand()
            ->select('salesman_id')->from($this->tableName());
        $command->setDistinct(true);
        return $command->queryColumn();
    }
    
    public function scopes(){
        return array(
            'incomplete' => array(
                'condition'=> 'is_history=:is_history',
                'params'=> array(':is_history' => 0),
            ),
            'history' => array(
                'condition'=> 'is_history=:is_history',
                'params'=> array(':is_history' => 1),
            ),
            //通过审批销售单
            'hasPass' => array(
                'condition' => 'approval_status=:approval_status',
                'params' => array(':approval_status' => PssFlow::APPROVAL_PASS),
            ),
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
            'items'=> array(self::HAS_MANY, self::$itemModel, 'order_id'),
            'products'=> array(self::HAS_MANY, 'Product', array('product_id' => 'id'), 'through' => 'items'),
            'outForms' => array(self::HAS_MANY, 'StockOut', 'sales_order_id'),
            'outItems' => array(self::HAS_MANY, 'StockOutItem', array('id' => 'form_id'), 'through' => 'outForms'),
            'receivedPrice' => array(self::STAT, 'ReceiveItem', 'order_id', 'select' => 'SUM(price)'),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
		);
	}
	


    public function getNotReceivedPrice(){
        return $this->total_price-$this->receivedPrice;
    }
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'no' => '销售单号',
			'user_id' => 'User',
			'salesman_id' => '业务员',
			'salesman' => '业务员',
			'salesman_dept_id' => '所在部门',
			'salesman_dept' => '所在部门',
			'corp_name' => '我方公司名称',
			'address' => '我方联系地址',
			'linkman' => '我方联系人',
			'phone' => '电话',
			'customer_id' => '客户',
			'customer_name' => '客户名称',
			'customer_address' => '客户联系地址',
			'customer_linkman' => '客户联系人',
			'customer_phone' => '客户电话',
			'delivery_date' => '交付期限',
			'balance_date' => '结算期限',
			'remark' => '备注',
			'total_price' => '应收金额',
			'status' => '订单状态',
			'approval_id' => '审批状态',
			'created' => '填单时间',
			'updated' => '修改时间',
			'items' => '产品信息',
			'receivedPrice' => '已收金额',
			'keyword' => '关键字',
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
        
        if (!empty($this->keyword)){
            $cr=new CDbCriteria;
            $cr->with = array('items');
            $cr->together = true;
            $cr->compare('no', $this->keyword, true, "OR")
               ->compare('customer_name', $this->keyword, true, "OR")
               ->compare('items.product_name', $this->keyword, true, "OR");
            $criteria->mergeWith($cr);
        }
        
        if(!empty($this->approval_id)){
            $criteria->join = "left join core_flow_task cft on cft.id=t.approval_id";
            $criteria->condition = "cft.status=".$this->approval_id;
            //$criteria->condition = "t.approval_status=".$this->approval_id;
        }
        $criteria->compare('salesman_dept_id', $this->salesman_dept_id);
        $criteria->compare('salesman_id', $this->salesman_id);
        $criteria->compare('t.status',$this->status);
        $criteria->mergeWith($this->compareDate('created'));
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),
        ));
    }
    
    public static function getStatusOptions(){
        return array(
            self::STATUS_INIT => '初始',
            self::STATUS_BUYING => '采购中',
            self::STATUS_DELIVERING => '发货中',
            self::STATUS_DELIVERED => '已发货',
        );
    }
    
    public function updateStatus($status){
        if ($status > $this->status && array_key_exists($status, self::getStatusOptions())){
            $this->status = $status;
            $this->save();
        }
    }
}
