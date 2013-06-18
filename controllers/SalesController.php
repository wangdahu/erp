<?php

class SalesController extends PssController
{
    public function init(){
        parent::init();
        $this->breadcrumbs['销售单'] = array('/pss/sales/index');
    }
    
	public function actionIndex()
	{
		$this->breadcrumbs[] = '进行中的销售单';
		$salesOrder = $this->createSearchModel()->incomplete();
		$this->render('index', array('model' => $salesOrder));
	}
	
	/**
	 * 结单操作
	 */
    public function actionStatement(){
        if (isset($_GET['id'])){
            $this->loadOrder($_POST['id'])->intoHistory();
            Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'结单成功！')));
            Yii::app()->end(CJSON::encode(array('status' => 1)));
        }
        Yii::app()->user->setFlash('page_flash', json_encode(array('msg' => '结单失败！', 'type' => 'warn')));
        Yii::app()->end(CJSON::encode(array('status' => 0)));
    }
    
    /**
     * 历史销售单删除
     */
    public function actionDeletehistory($id){
        SalesOrder::model()->deleteByPk($id);
        Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'删除成功！')));
        Yii::app()->end(CJSON::encode(array('status' => 1)));
    }
    
    protected function processOrderCommand(){

    }
    
    /**
     * 警告  单据未审批通过，不能结单！
     */
    public function actionWarn(){
        
    }
	
    public function actionHistory(){
        $this->breadcrumbs[] = '历史销售单';
        $salesOrder = $this->createSearchModel()->history();
        $this->render('index', array('model' => $salesOrder));
    }

    public function actionCreate(){
        $this->breadcrumbs['进行中销售单'] = array('/pss/sales/index');
        $this->breadcrumbs[] = '新增销售单';
        $salesOrder=new SalesOrder;
        $salesOrder->salesman = Yii::app()->user->name;
        $salesOrder->salesman_id = Yii::app()->user->id;
        $salesOrder->salesman_dept_id = Yii::app()->user->department_id;
        $salesOrder->salesman_dept = Account::department(Yii::app()->user->department_id)->name;
        $salesOrder->corp_name = Account::corp()->name;
        $salesOrder->address = Account::corp()->address;
        
        $items = $this->getOrderItems();
        if(isset($_POST['SalesOrder'])){
            $salesOrder->attributes = $_POST['SalesOrder'];
            $salesOrder->items = $items;
            
            if($salesOrder->save()){
                $approval = $salesOrder->createApproval($salesOrder->approval_id);
                $res = PssFlow::verifyNodeAuthority($approval['node_id'], $approval['task_id']);
                if (isset($res['prime']) && $res['prime'] === true) {
                    //自动审批
                    PssFlow::approved($approval['task_id'], $approval['node_id'], $res['node_relate_id'], '通过', 2);
                }
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加成功')));
                $this->redirect(array('view', 'id' => $salesOrder->id));
            }
        }
        $this->render('form',array('model'=>$salesOrder, 'items'=>$items));
    }
    
    public function actionView(){
        $salesOrder = $this->loadOrder();
        $this->breadcrumbs['进行中销售单'] = array('/pss/sales/index');
        $this->breadcrumbs[] = "销售单详情";
        
        $this->render('view', array('model' => $salesOrder));
    }
    
    public function actionUpdate($id){
        $this->breadcrumbs['进行中销售单'] = array('/pss/sales/index');
        $this->breadcrumbs[] = "销售单详情编辑";
        
        $salesOrder = SalesOrder::model()->findByPk($id);
        $items = $this->getOrderItems();
        
        if(isset($_POST['SalesOrder'])){
            foreach ($salesOrder->items as $item) {//删除产品信息
                $item->delete();
            }
            $salesOrder->attributes=$_POST['SalesOrder'];
            $salesOrder->items = $items;
            if($salesOrder->save()){
                //提醒第一审批人“XXX修改了XX单”
                $node_id = FlowProcess::getCurrentNode($salesOrder->approval_id);
                PssFlow::noticeUser($node_id, $salesOrder->approval_id, 6);
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'修改成功')));
                $this->redirect(array('view', 'id' => $salesOrder->id));
            }
        }
        $this->render('form',array('model'=>$salesOrder, 'items'=>$items));
    }
    
    public function actionPopup(){
        $order = $this->createSearchModel()->incomplete()->hasPass();
        $this->renderPartial('popup', array('model' => $order), false, true);
    }
    
    public function actionItems(){
        $criteria = new CDbCriteria();
        $criteria->with = 'items';
        $salesOrder = $this->loadOrder();
        $salesOrder->setDbCriteria($criteria);
        
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$_GET['id']}-product-item",
            'dataProvider'=> new CArrayDataProvider($salesOrder->items, array('pagination' => false)),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '销售产品', 'value' => '$data->product_name'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '单价', 'value' => '$data->price', 'headerHtmlOptions' => array('class' => 'span3'),),
                array('name' => '数量', 'value' => '$data->quantity', 'headerHtmlOptions' => array('class' => 'span2'),),
                array('name' => '应收金额', 'value' => '$data->totalPrice', 'headerHtmlOptions' => array('class' => 'span3'),),
                array('header' => '已出货', 'value' => '$data->outQuantity', 'headerHtmlOptions' => array('class' => 'span2'),),
                array('header' => '当前库存', 'value' => '$data->product->totalStock', 'headerHtmlOptions' => array('class' => 'span2'),),
                array(
                    'header'=>'催办记录',
                    'headerHtmlOptions' => array('style' => 'width: 140px'),
                    'type' => 'raw',
                    'value' => '($data->urgedCount ? CHtml::link("已催办&nbsp;{$data->urgedCount}次", array("urgedView", "item_id" => $data->id), array("class"=>"js-dialog-link", "data-title"=>"催办查看")) : "已催办&nbsp;{$data->urgedCount}次").
                               "&nbsp;&nbsp;/&nbsp;&nbsp;".CHtml::link("催办", array("createUrged", "item_id" => $data->id), array("class"=>"js-dialog-link"))',
                )
            ),
        ));//'已催办.$data->urgedCount.次.
    }
    
    //催办新增
    public function actionCreateUrged($item_id){
        $item = SalesOrderItem::model()->findByPk($item_id);
        
        $model = new BuyUrged();
        $model->item_id = $item_id;
        $model->user_id = Yii::app()->user->id;
        
        
        if(isset($_POST['BuyUrged'])){
            $notices = array();
            
            $trans = Yii::app()->db->beginTransaction();
            try{
                $model->attributes = $_POST['BuyUrged'];
                if ($model->save()){
                    $model->refresh();
                    $userIds = array();
                    $assigments = BuyAssignment::model()->findAll("product_id={$item->product_id}");
                    if (!$assigments){
                        foreach (PssUser::model()->findAll() as $user){
                            if (PssPrivilege::buyCheck(PssPrivilege::BUY_ADMIN, $user->id)){
                                $userIds[] = $user->id;
                            }
                        }
                    }else{
                        foreach ($assigments as $assigment){
                            if ($assigment->type == BuyAssignment::TYPE_ROLE){
                                $users = PssRole::model()->find($assigment->assign_id)->getUsers();
                                foreach ($users as $user){
                                    $userIds[] = $user->id;
                                }
                            }else{
                                $userIds[] = $assigment->assign_id;
                            }
                        }
                    }
                    $userIds = array_unique($userIds);
                    
                    foreach ($userIds as $userId){
                        $member = new BuyUrgedRelate();
                        $member->urged_id = $model->id;
                        $member->from_uid = Yii::app()->user->id;
                        $member->from_name = Yii::app()->user->name;
                        $member->to_uid = $userId;
                        $member->to_name = Account::user($userId)->name;
                        $notices[] = array('app'=>'pss','to_uid'=>$userId,'msg'=>"您好，".Yii::app()->user->name."向您发布了一个采购催办提醒",'url'=>Yii::app()->createUrl('/pss/buy/plan'));
                        $member->save();
                    }
                }
                $trans->commit();
                In::notice($notices);
                Yii::app()->user->setFlash('page_flash', CJSON::encode(array('msg' => '催办成功')));
                $this->redirect("index.php?r=pss/sales/index");
            }catch (CException $e){
                $trans->rollback();
                Yii::app()->user->setFlash('page_flash', CJSON::encode(array('msg' => '催办失败'.$e->getMessage(), 'type' => 'error')));
                $this->redirect("index.php?r=pss/sales/index");
            }
        }
        $this->renderPartial('urgedForm', array('item' => $item, 'model' => $model), false, true);
    }
    
    //催办查看及回复
    public function actionUrgedView($item_id){
        $model = new BuyUrgedReply();
        
        if (isset($_POST['BuyUrgedReply'])){
            $model->attributes = $_POST['BuyUrgedReply'];
            $model->user_id = Yii::app()->user->id;
            if($model->save()){
                $model->refresh();
                In::notice(array(array('app'=>'pss','to_uid'=>$model->buyUrged->user_id,
                        'msg'=>"您好，".Yii::app()->user->name."回复了您的采购催办",'url'=>Yii::app()->createUrl('/pss/sales'))));
                Yii::app()->user->setFlash('page_flash', CJSON::encode(array('msg' => '回复成功')));
                $this->redirect("index.php?r=pss/sales/index");
            }else{
                Yii::app()->user->setFlash('page_flash', CJSON::encode(array('msg' => '回复失败', 'type' => 'error')));
                $this->redirect("index.php?r=pss/sales/index");
            }
        }
        
        $model = new BuyUrged('search');
        $model->unsetAttributes();
        $model->item_id = $item_id;
        $dataProvider = $model->search();
        
        echo $this->processOutput($this->widget('zii.widgets.CListView', array(
            'id' => 'urged-view',
            'dataProvider' => $dataProvider,
            'itemView'=>'urgedView',   // refers to the partial view named '_post'
            'template' => "{items}\n{pager}",
            //'cssFile' => false,
            //'ajaxUrl' => $this->createUrl($this->route),
        ), true));
    }
    
    /**
     * @return SalesOrder
     */
    protected function createSearchModel(){
        $salesOrder = new SalesOrder('search');
        $salesOrder->unsetAttributes();
        if(isset($_GET['SalesOrder'])){
            $salesOrder->attributes = $_GET['SalesOrder'];
        }
        return $salesOrder;
    }
    
    /**
     * @return SalesOrder
     */
    protected function loadOrder($id=null){
        $model = SalesOrder::model()->findByPk($id ? $id : $_GET['id']);
        if ($model == null){
            // 由页面来源判定是否有上一步
            $url = isset($_SERVER['HTTP_REFERER']) ? '<a href="javascript:;" onclick="history.go(-1)">返回上一步</a>' : '<a href="index.php">返回OA主页</a>';
            throw new CHttpException(500, '很抱歉，您无权查看当前页面内容，请联系管理员进行授权。'.$url);
        }
        return $model;
    }
    
    protected function getOrderItems(){
        $items = array();
        if (isset($_POST['SalesOrderItem'])){
            $itemData = $_POST['SalesOrderItem'];
            foreach ($itemData as $val){
                $item = new SalesOrderItem;
                $item->attributes = $val;
                $items[] = $item;
            }
        }
        return $items;
    }
	
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
