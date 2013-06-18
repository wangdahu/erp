<?php

class BackbuyController extends ErpController
{
    
    public $defaultAction='plan';
    
    public function init(){
        parent::init();
        $this->breadcrumbs['采购单'] = array('/erp/buy');
    }
	
	public function actionIndex(){
	    $this->breadcrumbs[] = '采购退货';
	    $model = new BackBuy('search');
	    $model->unsetAttributes();
	    if (isset($_GET['BackBuy'])){
	        $model->attributes = $_GET['BackBuy'];
	    }
	    $model->hasBind();
	    $this->render('index', array('model' => $model));
	}
	
	public function actionCreate($order_id=''){
	    $this->breadcrumbs['进行中采购单'] = array('/erp/buy/index');
	    $this->breadcrumbs[] = '新增采购退货';
	    $model=new BackBuy();
	    
	    $model->back_id = Yii::app()->user->id;
	    $model->back_name = Yii::app()->user->name;
	    $model->back_dept_id = Account::department(Yii::app()->user->department_id)->id;
	    $model->back_dept = Account::department(Yii::app()->user->department_id)->name;
	    $model->buyer_dept_id = Account::department(Yii::app()->user->department_id)->id;
	    $model->buyer_dept = Account::department(Yii::app()->user->department_id)->name;
	    $model->corp_name = Account::corp()->name;
        $model->address = Account::corp()->address;
	    $buyOrder = null;
        if (isset($_GET['BackBuy'])){
            $model->attributes=$_GET['BackBuy'];
        }
        
        if (!empty($order_id)){
            $model->bindBuyOrder($order_id);
            $buyOrder = BuyOrder::model()->findByPk($order_id);
        }
        $model->items = $this->getItems($order_id);
        
        if(isset($_POST['BackBuy'])){
            $model->attributes=$_POST['BackBuy'];
            
            if($model->save()){
                $approval = $model->createApproval($model->approval_id);
                $res = ErpFlow::verifyNodeAuthority($approval['node_id'], $approval['task_id']);
                if (isset($res['prime']) && $res['prime'] === true) {
                    //自动审批
                    ErpFlow::approved($approval['task_id'], $approval['node_id'], $res['node_relate_id'], '通过', 2);
                }
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加成功')));
                $this->redirect(array('view', 'id' => $model->id));
            }else{
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加失败', 'type'=>'warn')));
            }
	    }
	    $this->render('form',array('model'=>$model, 'relatedOrder'=>$buyOrder));
	}
	
	public function actionUpdate($id){
	    $this->breadcrumbs['采购退货'] = array('/erp/backbuy/index');
        $this->breadcrumbs[] = "采购退货详情编辑";
        $model = BackBuy::model()->findByPk($id);
        $buyOrder = BuyOrder::model()->findByPk($model->order_id);
        if(isset($_POST['BackBuy'])){
            $model->attributes=$_POST['BackBuy'];
            foreach ($model->items as $item){
                $item->delete();
            }
            $model->items = $this->getItems($model->order_id);
            if($model->save()){
                //提醒第一审批人“XXX修改了XX单”
                $node_id = FlowProcess::getCurrentNode($model->approval_id);
                ErpFlow::noticeUser($node_id, $model->approval_id, 6);
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'修改成功')));
                $this->redirect(array('view', 'id' => $model->id));
            }else{
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'修改失败', 'type'=>'warn')));
            }
	    }
        $this->render('form',array('model'=>$model, 'relatedOrder'=>$buyOrder));
	}
	
    public function actionView($id){
        $this->breadcrumbs['采购退货'] = array('/erp/backbuy/index');
        $this->breadcrumbs[] = "采购退货详情";
        $order = $this->loadOrder($id);
        
        $this->render('view', array('model' => $order));
    }
    
    /**
     * @return BackBuy
     */
    protected function loadModel($id=null){
        $model = BackBuy::model()->findByPk($id ? $id : $_GET['id']);
        if ($model == null){
            // 由页面来源判定是否有上一步
            $url = isset($_SERVER['HTTP_REFERER']) ? '<a href="javascript:;" onclick="history.go(-1)">返回上一步</a>' : '<a href="index.php">返回OA主页</a>';
            throw new CHttpException(500, '很抱歉，您无权查看当前页面内容，请联系管理员进行授权。'.$url);
        }
        return $model;
    }
	
	/**
	 * @return BackBuy
	 */
	protected function loadOrder($id=null){
	    return $this->loadModel($id ? $id : $_GET['id'], 'BackBuy');
	}
	
	public function actionItems(){
        $criteria = new CDbCriteria();
        $criteria->with = 'items';
        $salesOrder = $this->loadOrder();
        $salesOrder->setDbCriteria($criteria);
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$_GET['order_id']}-product-item",
            'dataProvider'=> new CArrayDataProvider($salesOrder->items, array('pagination' => false)),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '采购产品', 'value' => '$data->product_name'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '单价', 'value' => '$data->price'),
                array('name' => '采购数量', 'value' => '$data->quantity'),
                array('name' => '应付金额', 'value' => '$data->totalPrice'),
                array('header' => '已入库数量', 'value' => '未开发'),
                array('header' => '当前库存', 'value' => '$data->product->totalStock'),
            ),
        ));
    }
    
    //货品入库 入库单  下拉详情
    public function actionDownItems($id){
        $criteria = new CDbCriteria();
        $model = $this->loadModel($id, 'BackBuy');
        $model->setDbCriteria($criteria);
        
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$id}-product-item",
            'dataProvider'=> new CArrayDataProvider($model->items),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '产品名称', 'value' => '$data->product_name'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '单价', 'value' => '$data->price'),
                array('name' => '应付金额', 'value' => '$data->totalPrice'),
                array('name' => '退货数量', 'value' => '$data->quantity'),
                array('header' => '当前库存', 'value' => '$data->stock->quantity'),
            ),
        ));
    }
    
    protected function getItems($order_id=''){
        $items = array();
        if (isset($_POST['BackBuyItem'])){
            $itemData = $_POST['BackBuyItem'];
            foreach ($itemData as $val){
                $item = new BackBuyItem;
                $item->attributes = $val;
                $items[] = $item;
            }
        }elseif (!empty($order_id)){
            $buyOrder = BuyOrder::model()->findByPk($order_id);
            foreach ($buyOrder->products as $k => $product){
                $item = new BackBuyItem;
                $item->product_id = $product->id;
                $item->product_name = $product->name;
                $item->product_no = $product->no;
                $item->product_brand = $product->brand->name;
                $item->product_unit = $product->unit->name;
                $item->product_cate = $product->cate->name;
                $item->quantity = $buyOrder->items[$k]->inQuantity;
                $item->price = $buyOrder->items[$k]->price;
                $items[] = $item;
            }
        }
        return $items;
    }
    
    protected function processOrderCommand(){
        if (isset($_POST['command'], $_POST['id']) && $_POST['command'] == "statement"){
            $this->loadOrder($_POST['id'])->intoHistory();
            $this->refresh();
        }
    }
    
    /**
     * @return BuyOrder
     */
    protected function createSearchModel(){
        $order = new BuyOrder('search');
        $order->unsetAttributes();
        if(isset($_GET['SalesOrder'])){
            $salesOrder->attributes = $_GET['SalesOrder'];
        }
        return $order;
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
