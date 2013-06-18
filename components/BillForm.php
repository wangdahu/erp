<?php


abstract class BillForm extends ActiveRecord {
    
    private $_approval = null;
    
    /**
     * (non-PHPdoc)
     * @see CModel::behaviors()
     */
    public function behaviors(){
        return array(
            'searchAttribute' => array(
                'class' => 'erp.models.behaviors.SearchAttribute',
            ),
            'timestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'updated',
            ),
        );
    }
    
    /**
     * 审批状态下拉框值
     * @return CHtml::listData
     */
    public function getApproveSelectValue(){
        $select_array = array(
            array('key'=>'', 'value'=> "请选择"), 
            array('key'=>'1', 'value'=> "流转中"), 
            array('key'=>'2', 'value' => "通过"), 
            array('key'=>'3', 'value'=> "不通过"),
            array('key'=>'4', 'value'=> "撤销"), 
            array('key'=>'5', 'value'=> "作废"),
         );
        return CHtml::listData($select_array, 'key', 'value');
    }
    
    /**
     * 审批状态下拉框值
     * @return CHtml::listData
     */
    public static function getStaticApproveSelectValue(){
        return self::getApproveSelectValue();
    }
    
    /**
     * 获取进销存审批流程
     * @return array
     */
    public function getFlows(){
        $flow_array = array();
        $form_flow = FormFlow::model()->findAll("form_name=:class", array(':class' => get_class($this)));
        
        foreach ($form_flow as $f_flow){
            $flow_info = Flow::getFlowById($f_flow['flow_id']);
            if($flow_info['is_history'] == '0' && $flow_info['deleted'] == '0'){
                array_push($flow_array, $f_flow);
            }
        }
        return $flow_array;
    }
    
    /**
     * 获取审批状态
     */
    public function getApproveStatus(){
        return $this->approval_status;
    }
    
    /**
     * 是否通过审批
     */
    public function getIsPassApprove(){
        return $this->approval_status == ErpFlow::APPROVAL_PASS;
    }
    
    /**
     * 审批状态字符创读取
     */
    public function getApproveStatusText(){
        return ErpFlow::approveStatusConvert($this->getApproveStatus());
    }
    
    /**
     * 获取进销存审批流程
     * @return array
     */
    public static function getErpFlows(){
        return FormFlow::getFlows(__CLASS__);
    }
	
    public function validBillNumber($attribute, $params){
        $result = $this->getDbConnection()
            ->createCommand()->select()
            ->from('erp_number')
            ->where('no=:no', array(':no'=>$this->$attribute))->queryRow();
        if ($result){
            $this->addError($attribute, isset($params['message']) ? $params['message'] : '单号已存在');
        }
    }
    
    private function _markUseBillNumber(){
        if ($this->isNewRecord){
            $number = new Number;
            $number->no = $this->billNumber();
            $number->save();
        }
    }
    
    /**
     * @return string
     */
    abstract public function billNumber();
    
    protected function beforeValidate(){
        if (parent::beforeValidate()){
            //if (!$this->isNewRecord) return true;
            $flag = true;
            if (empty($this->items)){
                $error = "至少选择一种产品";
                $this->addError('items', $error);
                Yii::app()->user->setFlash("page_flash", json_encode(array("msg" => $error, "type" => "warn")));
                return false;
            }
            
            foreach ($this->items as $item){
                if (!$item->validate()){
                    $flag = false;
                    break;
                }
            }
        }
        
        if ($flag){
            //if ($this->getDbConnection()->getCurrentTransaction() === null){
            //    $this->getDbConnection()->beginTransaction();
            //}
            return true;
        }
        return false;
    }
    
    protected function afterSave(){
        parent::afterSave();
        //if ($this->getDbConnection()->getCurrentTransaction() == null){
        //    $transaction = $this->getDbConnection()->beginTransaction();
        //}else{
        //    $transaction = $this->getDbConnection()->getCurrentTransaction();
        //}
        
        //var_dump($transaction);exit;
        if ($this->isNewRecord){
            $this->_markUseBillNumber();
        }
        
        foreach ($this->items as $item){
            $item->setAttribute($item->belongId(), $this->getPrimaryKey());
            if (!$item->save(false)){
                //$transaction->rollback();
                //var_dump($item->getErrors());
                //exit;
                return;
                //var_dump($item->getErrors());
                //$this->getDbConnection()->getCurrentTransaction()->rollback();
            }
        }
        //$transaction->commit();
        /* if ($this->getApproval()->getIsComplete()){
    
        } */
        
        
        $this->getApproval()->start();
        
    }
    
    
    private function _sendNotification($event){
        //In::send($data);
    }
    
    public function approvalStart(){
        
    }
    
    public function approvalPass(){
        
    }
    
    public function approvalFail(){
        
    }
    
    public function getApprovalStatus(){
        //return $this->getApproval()
    }
    
    public function createApproval($flow_id){
        $res = ErpFlow::initFlowTaskByFlowId($flow_id, 0);
        $this->approval_id = $res['task_id'];
        $this->save(false);
        $param = ErpFlow::verifyNodeAuthority($res['node_id'], $res['task_id']);
        if (!isset($param['prime']) || $param['prime'] === false) {//自审
            ErpFlow::noticeUser($res['node_id'], $res['task_id'], 0);
        }
        return $res;
    }
    
    /**
     * @return Approval
     */
    public function getApproval(){
        if ($this->_approval == null){
            $this->_approval = new Approval($this);
            //$this->_approval->attachEventHandler('onAfterStart', array($this, "_sendNotification"));
            //$this->_approval->attachEventHandler('onAfterStart', array($this, "_sendNotification"));
            //$this->_approval->attachEventHandler('onAfterStart', array($this, "_sendNotification"));
        }
        return $this->_approval;
    }
    
    public function saveItems(array $items){
        $flag = true;
        foreach ($items as $item){
            if (!$this->addItem($item)){
                $flag = false;
            }
        }
        return $flag;
    }
    
    public function addItem(BillFormItem $item){
        $product = Product::model()->findByPk($item->product_id);
        $item->setAttribute($item->belongId(), $this->id);
        $item->product_name = $product->name;
        $item->product_no = $product->no;
        $item->product_brand = $product->brand->name;
        $item->product_unit = $product->unit->name;
        $item->product_cate = $product->cate->name;
        return $item->save();
    }
    
    public function removeItem($itemId){
        foreach ($this->_items as $key => $item){
            if ($item->product_id == $itemId){
                unset($this->_items[$key]);
            }
        }
    }
    
    public function hasItem($itemId){
        foreach ($this->items as $item){
            if ($item->product_id == $itemId) return true;
        }
        return false;
    }
    

}
?>
