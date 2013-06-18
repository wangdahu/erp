<?php

class AssignController extends PssController{

    public function actionDelete($id, $type){
        $model = $type == 1 ? PssRole::model()->find($id) : PssUser::model()->find($id);
        if ($model != null){
            foreach ($model->getBuyAssignments() as $assigment){
                $assigment->delete();
            }
        }
        Yii::app()->end(CJSON::encode(array('status' => 1)));
    }
    
    public function existAssignmentItem($assign_id){
        return BuyAssignment::model()->find('assign_id in('.implode(',', $assign_id).')');
    }
    
    //采购产品非配
    public function actionIndex(){
        
        if (Yii::app()->request->isPostRequest){
            if (isset($_POST['role_id'])){
                
                if(!$this->existAssignmentItem($_POST['role_id'])){
                    foreach ($_POST['role_id'] as $role_id){
                        if(!empty($_POST['product_id'])){
                            foreach ($_POST['product_id'] as $product_id) {
                                $model = new BuyAssignment;
                                $model->assign_id = $role_id;
                                $model->product_id = $product_id;
                                $model->type = 1;
                                $model->save();
                            }
                        }else{
                            Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'请选择负责采购产品！', 'type'=>'warn')));
                            break;
                        }
                    }
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'角色已存在！', 'type'=>'warn')));
                }
            }
            if (isset($_POST['user_id'])){
                
                if(!$this->existAssignmentItem($_POST['user_id'])){
                    foreach ($_POST['user_id'] as $user_id){
                        if(!empty($_POST['product_id'])){
                            foreach ($_POST['product_id'] as $product_id) {
                                $model = new BuyAssignment;
                                $model->assign_id = $user_id;
                                $model->product_id = $product_id;
                                $model->type = 0;
                                $model->save();
                            }
                        }else{
                            Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'请选择负责采购产品！', 'type'=>'warn')));
                            break;
                        }
                    }
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'用户已存在！', 'type'=>'warn')));
                }
            }
            $this->redirect(Yii::app()->request->urlReferrer);
        }
    
        $buyers = array();
        foreach (PssRole::model()->findAll() as $role){
            if ($role->hasBuyAssignment()){
                $actor = new stdClass();
                $actor->id = $role->id;
                $actor->name = $role->name;
                $actor->type = BuyAssignment::TYPE_ROLE;
                $arr = array();
                foreach ($role->buyAssignments as $assignment){
                    $arr[] = $assignment->product->name;
                }
                $actor->products = implode('，', $arr);
                $buyers[] = $actor;
            }
        }
    
        foreach (PssUser::model()->findAll() as $user){
            if ($user->hasBuyAssignment()){
                $actor = new stdClass();
                $actor->id = $user->id;
                $actor->name = $user->name;
                $actor->type = BuyAssignment::TYPE_USER;
                $arr = array();
                foreach ($user->buyAssignments as $assignment){
                    $arr[] = $assignment->product->name;
                }
                $actor->products = implode('，', $arr);
                $buyers[] = $actor;
            }
        }
        echo $this->processOutput($this->widget('system.web.widgets.CTabView', array(
                'id' => 'product_assign',
                'htmlOptions' => array('style' => 'max-height:470px; overflow:auto;'),
                'tabs' => array(
                    'tab1' => array('title' => '采购产品分配', 'view' => 'tabViews/assign'),
                    'tab2' =>  array('title' => '查看分配', 'view' => 'tabViews/assignment')
                ),
                'viewData' => compact('model', 'buyers'),
        ), true));
        //$this->renderPartial('index', array('viewData' => compact('model', 'buyers')), false, true);
    }
    
    public function actionUpdate($id, $type){
        $model = $type == 1 ? PssRole::model()->find($id) : PssUser::model()->find($id);
        
        if(isset($_POST['product_id'])){
            BuyAssignment::model()->deleteAll("assign_id=$id and type=$type");
            foreach ($_POST['product_id'] as $pro_id){
                $buyassignment = new BuyAssignment();
                $buyassignment->assign_id = $id;
                $buyassignment->product_id = $pro_id;
                $buyassignment->type = $type;
                $buyassignment->insert();
            }
            Yii::app()->end(CJSON::encode(array('status' => 1)));
        }
        $this->renderPartial('update', array('model' => $model,), false, true);
    }
}