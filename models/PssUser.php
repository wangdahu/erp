<?php
class PssUser extends AppModel{
    
    private $_assigments;
    
    
    protected function dataProvider(){
        return Account::users(null);
    }
    
    /**
     * @param string $className
     * @return PssUser
     */
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    
    protected function salesCommand(){
        $salesOrder = SalesOrder::model();
        return $salesOrder->getDbConnection()
            ->createCommand()
            ->from($salesOrder->tableName().' t');
    }
    
    public function hasSalesOrder(){
        $command = $this->salesCommand()->select('salesman_id');
        $command->setDistinct(true);
        $command->where('t.approval_status='.PssFlow::APPROVAL_PASS);
        $ids = $command->queryColumn();
        $data = array();
        foreach ($this->getData() as $user){
            if (in_array($user->id, $ids)){
                $data[] = $user;
            }
        }
        $this->setData($data);
        return $this;
    }
    
    public function getSalesOrderCount(){
        return $this->salesCommand()
            ->select('COUNT(*)')
            ->where('salesman_id=:salesman_id and approval_status='.PssFlow::APPROVAL_PASS, array(':salesman_id'=>$this->getId()))
            ->queryScalar();
    }
    
    public function getSalesTotalPrice(){
        return $this->salesCommand()
            ->select('SUM(total_price)')
            ->where('salesman_id=:salesman_id and approval_status='.PssFlow::APPROVAL_PASS, array(':salesman_id'=>$this->getId()))
            ->queryScalar();
    }
    
    public function getReceivedPrice(){
        return $this->salesCommand()
            ->select('SUM(pri.price)')
            ->join('pss_receive_item pri', 't.id=pri.order_id')
            ->where('t.salesman_id=:salesman_id and approval_status='.PssFlow::APPROVAL_PASS, array(':salesman_id'=>$this->getId()))
            ->queryScalar();
    }
    
    public function getNotReceivedPrice(){
        return sprintf("%.2f", $this->getSalesTotalPrice() - $this->getReceivedPrice());
    }
    
    /**
     * 所在的部门范围
     * @param int $id
     * @return PssUser
     */
    public function department($id){
        $data = array();
        foreach ($this->getData() as $user){
            if ($user->department_id == $id){
                $data[] = $user;
            }
        }
        $this->setData($data);
        return $this;
    }

    
    public function loadSalesOrders(){
        
    }

    
    public function getBuyAssignments(){
        if ($this->_assigments !== null){
            return $this->_assigments;
        }
        $criteria = new CDbCriteria();
        $criteria->params = array(':assign_id' => $this->getId());
        $criteria->condition = "assign_id=:assign_id AND type=0";
        return $this->_assigments = BuyAssignment::model()->findAll($criteria);
    }
    
    public function hasBuyAssignment(){
        $assignment = $this->getBuyAssignments();
        return !empty($assignment);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        return new CArrayDataProvider($this->findAll(), array(
            'pagination'=>array(
                'pageSize'=>50,
            ),
        ));
    }
    
    public function attributeLabels(){
        return array(
            'id' => '员工id',
            'salesOrderCount' => '销售单数',
            'salesTotalPrice' => '应收金额',
        );
    }
}