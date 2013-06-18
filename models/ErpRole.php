<?php

class ErpRole extends AppModel
{
    
    
    private $_assigments;
    
    /**
     * @param string $className
     * @return ErpUser
     */
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
    
    public function getBuyAssignments(){
        if ($this->_assigments !== null){
            return $this->_assigments;
        }
        $criteria = new CDbCriteria();
        $criteria->params = array(':assign_id' => $this->getId());
        $criteria->condition = "assign_id=:assign_id AND type=1";
        return $this->_assigments = BuyAssignment::model()->findAll($criteria);
    }
    
    public function getUsers(){
        $ids = Account::roleUsers($this->getId());
        $users = array();
        foreach (ErpUser::model()->findAll() as $user){
            if (in_array($user->getId(), $ids)){
                $users[$user->getId()] = $user;
            }
        }
        return $users;
    }
    
    public function hasBuyAssignment(){
        return !empty($this->buyAssignments);
    }
    /* (non-PHPdoc)
     * @see AppModel::dataProvider()
     */
    protected function dataProvider() {
        return Account::roles();
    }

}
