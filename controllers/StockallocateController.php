<?php
/**
 * 产品调拨
 * @author hjb
 *
 */
class StockallocateController extends ErpController{
    
    public function init(){
        parent::init();
        $this->breadcrumbs['货品库存'] = array('/erp/stock/index');
        $this->breadcrumbs['产皮调拨'] = array('/erp/stockallocate/index');
    }
    
    //产品调拨首页
    public function actionIndex(){
        $this->breadcrumbs[] = '调拨单';
        $model = new StockAllocate('search');
        if(isset($_GET['StockAllocate'])){
            $model->attributes = $_GET['StockAllocate'];
        }
        $this->render('index', array('model' => $model));
    }
    
    //调拨产品
    public function actionItem(){
        $this->breadcrumbs[] = '调拨产品';
        $model = new StockAllocateItem('search');
        if(isset($_GET['StockAllocateItem'])){
            $model->attributes = $_GET['StockAllocateItem'];
        }
        $this->render('item', array('model' => $model));
    }
    
    //货品入库 入库单  下拉详情
    public function actionItems($id){
        $criteria = new CDbCriteria();
        $model = $this->loadModel($id, 'StockAllocate');
        
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>"order-{$id}-product-item",
            'dataProvider'=> new CArrayDataProvider($model->items),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'template'=>'{items}',
            'columns'=>array(
                array('name' => '产品名称', 'type'=>'raw', 'value' => '$data->product_name.
                       CHtml::activeHiddenField($data, "[$data->from_stock_id]from_stock_id").
                       CHtml::hiddenField("StockAllocateItem[$data->from_stock_id][pro_id]", $data->product_id)'),
                array('name' => '型号', 'value' => '$data->product_no'),
                array('name' => '品牌', 'value' => '$data->product_brand'),
                array('name' => '产品类别', 'value' => '$data->product_cate'),
                array('name' => '单位', 'value' => '$data->product_unit'),
                array('name' => '所在仓库', 'type'=>'raw', 'value' => 'Storehouse::model()->find("id={$data->from_storehouse_id}")->name.CHtml::activeHiddenField($data, "[".Stock::model()->find("product_id={$data["product_id"]} and storehouse_id={$data->from_storehouse_id}")->id."]from_storehouse_id", 
                       array("value"=>"$data->from_storehouse_id"))', 'htmlOptions'=>array('class' => 'storehouse',)),
                array('name' => '当前库存', 'type'=>'raw', 'value'=>'Stock::model()->find("product_id=".$data["product_id"]." and storehouse_id=".$data->from_storehouse_id)->quantity', 'htmlOptions' => array('class' => 'stock')),
                array('name' => '转出数量', 'type'=>'raw', 'value'=>'$data->isNewRecord ? Html5::activeTextField($data, "[".Stock::model()->find("product_id={$data["product_id"]} and storehouse_id={$data->from_storehouse_id}")->id."]quantity", 
                        array("class"=>"span2", "min"=>"0", "max"=>Stock::model()->find("product_id=".$data["product_id"]." and storehouse_id=".$data->from_storehouse_id)->quantity, "value"=>"$data->quantity")) : $data->quantity',
                        'htmlOptions'=>array('class' => 'quantity')),
            ),
        ));
    }
    
    //新添调拨单
    public function actionCreate(){
        $this->breadcrumbs['调拨单'] = array('/erp/stockallocate/index');
        $this->breadcrumbs[] = '新添调拨单';
        $model = new StockAllocate();
        $model->user_id = Yii::app()->user->id;
        $model->allocate_man_id = Yii::app()->user->id;
        $model->allocate_name = Yii::app()->user->name;
        $model->allocate_dept_id = Yii::app()->user->department_id;
        $model->allocate_dept = Account::department(Yii::app()->user->department_id)->name;
        
        $model->items = $this->getStockAllocateItems();
        if(isset($_POST['StockAllocate'])){
            $model->attributes = $_POST['StockAllocate'];
            
            if($model->save()){
                $approval = $model->createApproval($model->approval_id);
                $res = ErpFlow::verifyNodeAuthority($approval['node_id'], $approval['task_id']);
                if (isset($res['prime']) && $res['prime'] === true) {
                    //自动审批
                    ErpFlow::approved($approval['task_id'], $approval['node_id'], $res['node_relate_id'], '通过', 2);
                }
                
                Yii::app()->user->setFlash('page-flash', CJSON::encode(array('msg'=>'保存成功！')));
                $this->redirect(array('index'));
            }
        }
        $this->render('form', array('model' => $model));
    }
    
    //调拨单修改
    public function actionUpdate($id){
        $this->breadcrumbs['产皮调拨'] = array('/erp/stockallocate/index');
        $this->breadcrumbs['调拨单'] = array('/erp/stockallocate/index');
        $this->breadcrumbs[] = '调拨单详情编辑';
        $model = StockAllocate::model()->findByPk($id);
        
        if(isset($_POST['StockAllocate'])){
            $model->attributes = $_POST['StockAllocate'];
            
            foreach ($model->items as $item){
                $item->delete();
            }
            
            $model->items = $this->getStockAllocateItems();
            if($model->save()){
                //提醒第一审批人“XXX修改了XX单”
                $node_id = FlowProcess::getCurrentNode($model->approval_id);
                ErpFlow::noticeUser($node_id, $model->approval_id, 6);
                
                Yii::app()->user->setFlash('page-flash', CJSON::encode(array('msg'=>'保存成功！')));
                $this->redirect(array('index'));
            }
        }
        
        $this->render('form', array('model' => $model));
    }
    
    //调拨单详情
    public function actionView($id){
        $this->breadcrumbs['产皮调拨'] = array('/erp/stockallocate/index');
        $this->breadcrumbs['调拨单'] = array('/erp/stockallocate/index');
        $this->breadcrumbs[] = '调拨单详情';
        $model = $this->loadModel($id, "StockAllocate");
        $this->render('view', array('model' => $model));
    }
    
    /**
     * @return StockAllocate
     */
    protected function loadModel($id=null){
        $model = StockAllocate::model()->findByPk($id ? $id : $_GET['id']);
        if ($model == null){
            // 由页面来源判定是否有上一步
            $url = isset($_SERVER['HTTP_REFERER']) ? '<a href="javascript:;" onclick="history.go(-1)">返回上一步</a>' : '<a href="index.php">返回OA主页</a>';
            throw new CHttpException(500, '很抱歉，您无权查看当前页面内容，请联系管理员进行授权。'.$url);
        }
        return $model;
    }
    
    //添加已有产品（弹出层内容）
    public function actionPopup(){
        $model = new Stock('search');
        $model->unsetAttributes();
        if(isset($_GET['Stock'])){
            $model->attributes = $_GET['Stock'];
        }
        $this->renderPartial('popup', array('model' => $model), false, true);
    }
    
    /**
     * 获取调拨产品信息
     */
    public function getStockAllocateItems(){
        $items = array();
        if(isset($_POST['StockAllocateItem'])){
            foreach ($_POST['StockAllocateItem'] as $item){
                $stock = Stock::model()->findByPk($item['from_stock_id']);
                $allocate_item = new StockAllocateItem();
                $allocate_item->attributes = $item;
                $allocate_item->product_id = $stock->product_id;
                $allocate_item->from_storehouse_id = $stock->storehouse_id;
                $allocate_item->to_storehouse_id = $_POST['StockAllocate']['storehouse_id'];
                array_push($items, $allocate_item);
            }
        }
        return $items;
    }
}
