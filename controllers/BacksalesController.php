<?php

class BacksalesController extends ErpController{
    
    public function init(){
        parent::init();
        $this->breadcrumbs['销售单'] = array('/erp/sales/index');
    }
    
    //销售退货产品
    public function actionIndex(){
        $this->breadcrumbs[] = '销售退货';
        $model = new BackSales('search');
        $model->unsetAttributes();
        if(isset($_GET['BackSales'])){
            $model->attributes = $_GET['BackSales'];
        }
        $this->render('index', array('model' => $model));
    }
    
    //新增退货
    public function actionCreate($order_id=''){
        $this->breadcrumbs['进行中销售单'] = array('/erp/sales/index');
        $this->breadcrumbs[] = '新增退货';
        
        $model=new BackSales();
        $model->user_id = Yii::app()->user->id;
        $model->back_id = Yii::app()->user->id;
        $model->back_name = Yii::app()->user->name;
        $model->back_dept_id = Yii::app()->user->department_id;
        $model->back_dept = Account::department(Yii::app()->user->department_id)->name;
        $model->corp_name = Account::corp()->name;
        $model->address = Account::corp()->address;
        $salesOrder = null;
        if (isset($_GET['BackSales'])){
            $model->attributes=$_GET['BackSales'];
        }
        
        if (!empty($order_id)){
            $model->bindSalesOrder($order_id);
            $salesOrder = SalesOrder::model()->findByPk($order_id);
        }
        $model->items = $this->getBackSalesItems($order_id);
        
        if(isset($_POST['BackSales'])){
            $model->attributes=$_POST['BackSales'];
            $model->salesman_dept_id = Account::user($_POST['BackSales']['salesman_id'])->department_id;
            $model->salesman_dept = Account::department(Account::user($_POST['BackSales']['salesman_id'])->department_id)->name;
            
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
        $this->render('form',array('model'=>$model, 'relatedOrder'=>$salesOrder));
    }
    
    public function actionUpdate($id){
        $this->breadcrumbs['销售退货'] = array('/erp/backsales/index');
        $this->breadcrumbs[] = "销售退货详情编辑";
        $model = BackSales::model()->findByPk($id);
        $salesOrder = SalesOrder::model()->findByPk($model->order_id);
        
        if(isset($_POST['BackSales'])){
            $model->attributes=$_POST['BackSales'];
            $model->salesman_dept_id = Account::user($_POST['BackSales']['salesman_id'])->department_id;
            $model->salesman_dept = Account::department(Account::user($_POST['BackSales']['salesman_id'])->department_id)->name;
            
            foreach ($model->items as $item){
                $item->delete();
            }
            
            $model->items = $this->getBackSalesItems($model->order_id);
            if($model->save()){
                //提醒第一审批人“XXX修改了XX单”
                $node_id = FlowProcess::getCurrentNode($model->approval_id);
                ErpFlow::noticeUser($node_id, $model->approval_id, 6);
                
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加成功')));
                $this->redirect(array('view', 'id' => $model->id));
            }else{
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'添加失败', 'type'=>'warn')));
            }
        }
        
        $this->render('form',array('model'=>$model, 'relatedOrder'=>$salesOrder));
    }
    
    //货品入库 入库单  下拉详情
    public function actionItems($id){
        $criteria = new CDbCriteria();
        $model = $this->loadModel($id, 'BackSales');
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
    
    public function actionView($id){
        $model = $this->loadModel($id, "backSales");
        $this->breadcrumbs['销售退货'] = array('/erp/backsales/index');
        $this->breadcrumbs[] = "销售退货详情";
        $this->render('view', array('model' => $model, 'items' => $model->items));
    }
    
    /**
     * @return BackSales
     */
    protected function loadModel($id=null){
        $model = BackSales::model()->findByPk($id ? $id : $_GET['id']);
        if ($model == null){
            // 由页面来源判定是否有上一步
            $url = isset($_SERVER['HTTP_REFERER']) ? '<a href="javascript:;" onclick="history.go(-1)">返回上一步</a>' : '<a href="index.php">返回OA主页</a>';
            throw new CHttpException(500, '很抱歉，您无权查看当前页面内容，请联系管理员进行授权。'.$url);
        }
        return $model;
    }
    
    public function actionPopup(){
        $order = $this->createSearchModel()->incomplete()->hasPass();
        $this->renderPartial('popup', array('model' => $order), false, true);
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
     * Enter description here ...
     * @param unknown_type $order_id
     */
    protected function getBackSalesItems($order_id=''){
        $items = array();
        if (isset($_POST['BackSalesItem'])){
            $itemData = $_POST['BackSalesItem'];
            foreach ($itemData as $val){
                $item = new BackSalesItem();
                $item->attributes = $val;
                $items[] = $item;
            }
        }elseif (!empty($order_id)){
            $order = SalesOrder::model()->findByPk($order_id);
            foreach ($order->products as $k => $product){
                $item = new BackSalesItem();
                $item->product_id = $product->id;
                $item->product_name = $product->name;
                $item->product_no = $product->no;
                $item->product_brand = $product->brand->name;
                $item->product_unit = $product->unit->name;
                $item->product_cate = $product->cate->name;
                $item->quantity = $order->items[$k]->quantity;
                $item->price = $order->items[$k]->price;
                $items[] = $item;
            }
        }
        return $items;
    }
}
