<?php

/**
 * This is the model class for table "pss_stock_out".
 *
 * The followings are the available columns in table 'pss_stock_out':
 * @property integer $id
 * @property string $no
 * @property integer $user_id
 * @property integer $sales_order_id
 * @property integer $out_id
 * @property string $out_name
 * @property integer $out_dept_id
 * @property string $out_dept
 * @property string $corp_name
 * @property string $address
 * @property string $linkman
 * @property string $phone
 * @property integer $customer_id
 * @property string $customer_name
 * @property string $customer_address
 * @property string $customer_linkman
 * @property string $customer_phone
 * @property string $remark
 * @property string $total_price
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 * @property integer $deleted
 * @property bool $isBindOrder
 */
class StockOut extends BillForm
{
    
    public function defaultScope(){
        if (!PssPrivilege::stockCheck(PssPrivilege::STOCK_VIEW) && PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
            return array(
                'condition' => 'out_id=:out_id',
                'params' => array(':out_id' => Yii::app()->user->id),
            );
        }
        return array();
    }
    
    public function billNumber(){
        return $this->no;
    }
    
    public function getIsBindOrder(){
        return !empty($this->sales_order_id);
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StockOut the static model class
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
		return 'pss_stock_out';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, out_id, out_name, out_dept_id, out_dept, corp_name, address, linkman, phone, customer_id, customer_name, customer_address, customer_linkman, customer_phone, total_price', 'required'),
	        array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('no', 'validBillNumber', 'on'=>'insert'),
	        array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
	        array('sales_order_id, out_id, out_dept_id, customer_id, deleted', 'numerical', 'integerOnly'=>true),
			array('no, out_name, out_dept, corp_name, linkman, phone, customer_name, customer_linkman', 'length', 'max'=>50),
			array('address, customer_address, customer_phone', 'length', 'max'=>100),
			array('total_price', 'length', 'max'=>10),
			array('remark', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, no, user_id, out_id, out_name, corp_name, address, linkman, phone, customer_id, customer_name, customer_address, customer_linkman, 
			       customer_phone, remark, total_price, approval_id, created,
			       date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
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
            'items' => array(self::HAS_MANY, 'StockOutItem', 'form_id', 'condition' => 'items.type=1'),
            'storehouses' => array(self::HAS_MANY, 'Storehouse', array('storehouse_id' => 'id'), 'through' => 'items'),
            'salesOrder' => array(self::BELONGS_TO, 'SalesOrder', 'sales_order_id'),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'id' => 'ID',
            'no' => '出库单号',
            'user_id' => '操作人ID',
            'sales_order_id' => '关联的销售单',
            'out_id' => '出库人',
		    'out_dept_id' => '出库人部门',
            'buy_order_id' => '关联的销售单',
            'out_name' => '出库人',
            'corp_name' => '我方公司名称',
            'address' => '通讯地址',
            'linkman' => '我方联系人',
            'phone' => '电话',
            'customer_id' => 'Customer',
            'customer_name' => '客户名称',
            'customer_address' => '通讯地址',
            'customer_linkman' => '客户联系人',
            'customer_phone' => '电话',
            'remark' => '备注',
            'total_price' => '总计金额',
            'approval_id' => '审批状态',
            'created' => '录入时间',
            'updated' => '更新时间',
            'deleted' => '删除',
            'items' => '产品信息',
		);
	}
	
    public function bindSalesOrder($orderId){
        $SalesOrder = SalesOrder::model()->findByPk($orderId);
        $this->sales_order_id = $orderId;
        $this->_bindAttributes($SalesOrder);
    }
    
    private function _bindAttributes(SalesOrder $salesOrder){
        $attributes = array('corp_name', 'address', 'linkman', 'phone', 
            'customer_id', 'customer_name', 'customer_address', 'customer_linkman', 'customer_phone', 'total_price');
        foreach ($attributes as $attr){
            $this->setAttribute($attr, $salesOrder->getAttribute($attr));
        }
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
        }
        
        $criteria->compare('out_dept_id', $this->out_dept_id);
        $criteria->compare('out_id',$this->out_id);
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),
        ));
    }
    
    protected function beforeValidate(){
        if (parent::beforeValidate()){
            $flag = true;
            foreach ($this->items as $item){
                $flag = $item->vaildQuantity($this) && $flag;
            }
            return $flag;
        }
        return false;
    }
    
    protected function afterSave(){
        parent::afterSave();
        
        //把绑定的销售单的状态进行调整
        if ($this->getIsBindOrder()){
            $salesOrder = SalesOrder::model()->with('items')->findByPk($this->sales_order_id);
            $status = SalesOrder::STATUS_DELIVERED;
            foreach ($salesOrder->items as $item){
                if ($item->quantity > $item->outQuantity){
                    $status = SalesOrder::STATUS_DELIVERING;
                    break;
                }
            }
            $salesOrder->updateStatus($status);
        }
    }
}