<?php

/**
 * This is the model class for table "erp_stock_in".
 *
 * The followings are the available columns in table 'erp_stock_in':
 * @property integer $id
 * @property string $no
 * @property integer $user_id
 * @property integer $buy_order_id
 * @property integer $in_id
 * @property string $in_name
 * @property integer $in_dept_id
 * @property string $in_dept
 * @property string $corp_name
 * @property string $address
 * @property string $linkman
 * @property string $phone
 * @property integer $supplier_id
 * @property string $supplier_name
 * @property string $supplier_address
 * @property string $supplier_linkman
 * @property string $supplier_phone
 * @property string $remark
 * @property string $total_price
 * @property integer $approval_id
 * @property integer $created
 * @property integer $updated
 * @property integer $deleted
 * @property StockInItem[] items
 */
class StockIn extends BillForm
{
    
    public function defaultScope(){
        if (!ErpPrivilege::stockCheck(ErpPrivilege::STOCK_VIEW) && ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
            return array(
                'condition' => 'in_id=:in_id',
                'params' => array(':in_id' => Yii::app()->user->id),
            );
        }
        return array();
    }
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return StockIn the static model class
     */
    public static function model($className=__CLASS__)
    {
    	return parent::model($className);
    }
    
    public function bindBuyOrder($orderId){
        $buyOrder = BuyOrder::model()->findByPk($orderId);
        $this->buy_order_id = $orderId;
        $this->_bindAttributes($buyOrder);
    }
    
    private function _bindAttributes(BuyOrder $buyOrder){
        $attributes = array('corp_name', 'address', 'linkman', 'phone', 'buyer_id', 'buyer',
            'supplier_id', 'supplier_name', 'supplier_address', 'supplier_linkman', 'supplier_phone', 'total_price');
        foreach ($attributes as $attr){
            $this->setAttribute($attr, $buyOrder->getAttribute($attr));
        }
    }
    
    public function getIsBindOrder(){
        return !empty($this->buy_order_id);
    }
	
    public function billNumber(){
        return $this->no;
    }

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'erp_stock_in';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, in_id, in_name, buyer_id, buyer, in_dept_id, in_dept, corp_name, address, linkman, phone, supplier_id, supplier_name, supplier_address, supplier_linkman, supplier_phone, total_price', 'required'),
	        array('approval_id', 'required', 'message' => '请选择审批流程'),
			array('no', 'validBillNumber', 'on'=>'insert'),
	        array('no', 'unique', 'className' => 'Number', 'attributeName' => 'no', 'on'=>'insert'),
	        array('buy_order_id, in_id, in_dept_id, supplier_id', 'numerical', 'integerOnly'=>true),
			array('no, in_name, in_dept, corp_name, linkman, phone, supplier_name, supplier_linkman', 'length', 'max'=>50),
			array('address, supplier_address, supplier_phone', 'length', 'max'=>100),
	        array('remark, user_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, no, user_id, buy_order_id, in_id, in_name, corp_name, address, linkman, phone, supplier_id, supplier_name, supplier_address, 
			       supplier_linkman, supplier_phone, total_price, approval_id, created, updated, deleted,
			       date_pattern, start_date, end_date, keyword', 'safe', 'on'=>'search'),
		);
	}
	
    protected function beforeSave(){
        if (parent::beforeSave()){
            if ($this->isNewRecord){
                $this->user_id = Yii::app()->user->id;
            }
            return true;
        }
        return false;
    }
    
    protected function afterSave(){
        parent::afterSave();
        /* if ($this->getApproval()->getIsComplete()){
            
        } */
        //把绑定的采购单的状态进行调整
        if ($this->getIsBindOrder()){
            $buyOrder = BuyOrder::model()->with('items')->findByPk($this->buy_order_id);
            $status = BuyOrder::STATUS_HAS_STORE;
            foreach ($buyOrder->items as $item){
                if ($item->quantity > $item->inQuantity){
                    $status = BuyOrder::STATUS_STORING;
                    break;
                }
            }
            $buyOrder->updateStatus($status);
        }
    }
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'items' => array(self::HAS_MANY, 'StockInItem', 'form_id', 'condition' => 'items.type=0'),
            'storehouses' => array(self::HAS_MANY, 'Storehouse', array('storehouse_id' => 'id'), 'through' => 'items'),
            'buyOrder' => array(self::BELONGS_TO, 'BuyOrder', 'buy_order_id'),
            'task' => array(self::BELONGS_TO, 'CoreFlowTask', 'approval_id'),
        );
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
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => '编号',
            'no' => '入库单号',
            'buy_order_id' => '关联的采购单',
            'in_id' => '入库人',
            'in_name' => '入库人',
            'in_dept_id' => '入库人部门',
            'in_dept' => '入库人部门',
            'user_id' => '录入人id',
            'buyer_id' => '采购员',
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
            'created' => '填单日期',
            'updated' => '修改时间',
            'items' => '产品信息',
            'approval_id' => '审批状态',
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
               ->compare('supplier_name', $this->keyword, true, "OR")
               ->compare('supplier_linkman', $this->keyword, true, "OR");
            
            $criteria->mergeWith($cr);
        }
        
        if(!empty($this->approval_id)){
            $criteria->join = "left join core_flow_task cft on cft.id=t.approval_id";
            $criteria->condition = "cft.status=".$this->approval_id;
        }
        
        $criteria->compare('in_dept_id', $this->in_dept_id);
        $criteria->compare('in_id', $this->in_id);
		$criteria->compare('buyer_id',$this->buyer_id,true);
		$criteria->mergeWith($this->compareDate('created'));
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination' => array('pageSize'=>30),
		));
	}
}
