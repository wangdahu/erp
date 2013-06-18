<?php

/**
 * This is the model class for table "pss_form_flow".
 *
 * The followings are the available columns in table 'pss_form_flow':
 * @property integer $id
 * @property string $form_name
 * @property integer $flow_id
 * @property integer $deleted
 */
class FormFlow extends ActiveRecord
{
    
    private $_flow = null;
    private $_nodes = array('apply_node' => array(), 'flow_node' => array(), 'notice_node' => array());
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormFlow the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
    public function afterFind(){
        $this->_flow = PssFlow::getFlowById($this->flow_id);
        $nodes = PssFlow::getNodeByFlowId($this->flow_id);
        foreach ($nodes as $node){
            
            switch ($node['type']){
                case 1: 
                    array_push($this->_nodes['apply_node'], $node['name']);
                    break;
                case 0:
                    array_push($this->_nodes['flow_node'], $node['name']);
                    break;
                case 2:
                    array_push($this->_nodes['notice_node'], $node['name']);
                    break;
            }
        }
    }
    
    /**
     * 是否可使用此审批
     * @param unknown_type $uid
     */
    public function getIsApply(){
        $bool = PssFlow::checkStartApproveAuthority($this->flow_id);
        return $bool;
    }
    
    public function getApplyNodes(){
        return $this->_nodes['apply_node'];
    }
    
    public function getNoticeNodes(){
        return $this->_nodes['notice_node'];
    }
    
    public function getApproveNodes(){
        //if (in_array(Yii::app()->user->id, $this->_nodes['apply'])){
            return $this->_nodes['flow_node'];
        //}
    }
    
    public function getName(){
        return $this->_flow['name'];
    }
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pss_form_flow';
	}
    
    public static function getLeftMenuList(){
        return array(
            'SalesOrder'     => '销售单', 
            'BuyOrder'       => '采购单', 
            'StockIn'        => '入库单', 
            'StockOut'       => '出库单', 
            'StockAllocate'  => '调拨单', 
            'BackSales'      => '销售退货单', 
            'BackBuy'        => '采购退货单'
        );
    }
    
    /**
     * 消息提醒链接地址
     * @param int $task_id
     */
    public static function noticeLink($form_name, $task_id){
        $form_name = empty($form_name) ? 'SalesOrder' : $form_name;
        $task_id = empty($task_id) ? '0' : $task_id;
        $id = "0";
        
        if($form_name == "SalesOrder"){
            $id = SalesOrder::model()->find("approval_id=".$task_id)->id;
        }else if($form_name == "BuyOrder"){
            $id = BuyOrder::model()->find("approval_id=".$task_id)->id;
        }else if($form_name == "StockIn"){
            $id = StockIn::model()->find("approval_id=".$task_id)->id;
        }else if($form_name == "StockOut"){
            $id = StockOut::model()->find("approval_id=".$task_id)->id;
        }else if($form_name == "StockAllocate"){
            $id = StockAllocate::model()->find("approval_id=".$task_id)->id;
        }else if($form_name == "BackSales"){
            $id = BackSales::model()->find("approval_id=".$task_id)->id;
        }else if($form_name == "BackBuy"){
            $id = BackBuy::model()->find("approval_id=".$task_id)->id;
        }
        
        $approve_link = array(
            'SalesOrder'      => "/pss/sales/view&id=", 
            'BuyOrder'        => "/pss/buy/view&id=", 
            'StockIn'         => "/pss/stockin/view&id=", 
            'StockOut'        => "/pss/stockout/view&id=", 
            'StockAllocate'   => "/pss/stockallocate/view&id=", 
            'BackSales'       => "/pss/backsales/view&id=", 
            'BackBuy'         => "/pss/backbuy/view&id=",
        );
        
        return $approve_link[$form_name].$id;
    }
    
    /**
     * 审批状态修改
     */
    public static function changeApproveStatus($task_id, $approve_status){
        $form_name = self::returnFormName($task_id);
        $detail = null;
        
        if($form_name == "SalesOrder"){                   //销售单
            $detail = SalesOrder::model()->findByPk(SalesOrder::model()->find("approval_id=".$task_id)->id);
            if($approve_status == '3' || $approve_status == '4')$detail->intoHistory();//置为历史   审批状态为3（不通过）、4（撤销）时，转至历史
        }else if($form_name == "BuyOrder"){               //采购单
            $detail = BuyOrder::model()->findByPk(BuyOrder::model()->find("approval_id=".$task_id)->id);
            if($approve_status == '3' || $approve_status == '4')$detail->intoHistory();//置为历史   审批状态为3（不通过）、4（撤销）时，转至历史
        }else if($form_name == "StockIn"){                //入库单
            $detail = StockIn::model()->findByPk(StockIn::model()->find("approval_id=".$task_id)->id);
        }else if($form_name == "StockOut"){               //出库单
            $detail = StockOut::model()->findByPk(StockOut::model()->find("approval_id=".$task_id)->id);
        }else if($form_name == "StockAllocate"){          //调拨单
            $detail = StockAllocate::model()->findByPk(StockAllocate::model()->find("approval_id=".$task_id)->id);
        }else if($form_name == "BackSales"){              //销售退货
            $detail = BackSales::model()->findByPk(BackSales::model()->find("approval_id=".$task_id)->id);
        }else if($form_name == "BackBuy"){                //采购退货
            $detail = BackBuy::model()->findByPk(BackBuy::model()->find("approval_id=".$task_id)->id);
        }
        
        $detail->approval_status = $approve_status;
        $detail->save(false);
    }
    
    public function returnFormName($task_id){
        $task_info = FlowTask::getTaskInfoById($task_id);
        $form_flow_info = FormFlow::model()->find('flow_id='.$task_info['flow_id']);
        return $form_flow_info->form_name;
    }
    
    public static function returnStaticFormName($task_id){
        return self::returnFormName($task_id);
    }
    
    /**
     * 撤销库存修改
     * @param int $task_id
     */
    public static function cancelRevocationStock($task_id){
        $form_name = self::returnFormName($task_id);
        
        if($form_name == "StockIn"){                        //入库单
            $id = StockIn::model()->find("approval_id=".$task_id)->id;
            $item = StockInItem::model()->find('type=0 and form_id='.$id);
            $item->stock->subtract($item->quantity);
        }else if($form_name == "StockOut"){                 //出库单
            $id = StockOut::model()->find("approval_id=".$task_id)->id;
            $item = StockOutItem::model()->find('type=1 and form_id='.$id);
            $item->stock->add($item->quantity);
        }else if($form_name == "StockAllocate"){            //调拨单
            $id = StockAllocate::model()->find("approval_id=".$task_id)->id;
            $item = StockAllocateItem::model()->find('allocate_id='.$id);
            $item->fromStock->add($item->quantity);
            $item->toStock->subtract($item->quantity);
        }
    }
    
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('form_name, flow_id', 'required'),
			array('flow_id, deleted', 'numerical', 'integerOnly'=>true),
			array('form_name', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, form_name, flow_id, deleted', 'safe', 'on'=>'search'),
		);
	}
    
	/**
     * 获取进销存审批流程  暂时废弃
     * @return array
     */
	public static function getFlows($form_name){
	    $res_array = array();
        //审批流程绑定表单查询
        $result = Yii::app()->db->createCommand()->select("cf.id as flow_id,pff.form_name,pff.flow_id,cf.name")->from('pss_form_flow pff')
              ->leftJoin('core_flow cf', 'cf.id=pff.flow_id')
              ->where('cf.is_history=0 and cf.deleted=0 and pff.form_name="'.$form_name.'"')->queryAll();
              
        foreach ($result as $k=>$v){
            $field_array = array();
            
            $field_array['flow_id'] = $v['flow_id'];
            $field_array['flow_name'] = $v['name'];
            
            //审批流程节点查询
            $node_result = Yii::app()->db->createCommand()->select("cfn.flow_id,cfn.name,cfn.type")->from("core_flow_node cfn")
                                         ->where("cfn.deleted=0 and flow_id='".$v['flow_id']."'")->queryAll();
            
            $flow_node_array = array();
            foreach ($node_result as $nres){
                if($nres['type'] == '1'){//适用人节点
                    $field_array['apply_node'] = $nres['name'];
                }else if($nres['type'] == '2'){//审批流程完成通知人员节点
                    $field_array['notice_node'] = $nres['name'];
                }else if($nres['type'] == '0'){//审批流程节点
                    array_push($flow_node_array, $nres['name']);
                }
            }
            $field_array['flow_node'] = $flow_node_array;
            array_push($res_array, $field_array);
        }
        return $res_array;
	}
    
    /**
     * 跟进流程ID获取审批流程所有节点 包含启动审批人 和 完成审批节点
     * @param $flow_id 流程ID 
     * @return array
     */
    public static function getNodeByFlowId($flow_id) {
        return FlowNode::getNodeByFlowId($flow_id);
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'form_name' => '流程名称',
			'flow_id' => '流程表ID',
			'deleted' => 'Deleted',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('form_name',$this->form_name,true);
		$criteria->compare('flow_id',$this->flow_id);
		$criteria->compare('deleted',$this->deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}