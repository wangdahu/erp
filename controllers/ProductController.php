<?php

class ProductController extends ErpController
{
    public function init(){
        parent::init();
        $this->breadcrumbs['货品库存'] = array('/erp/stock/index');
    }
    
    protected function processAdminCommand(){
        
    }
    
    public function actionList($cate_id=''){
        $this->breadcrumbs[] = '产品目录';
        $this->processAdminCommand();
        $cate = ProductCate::model()->findAll();
        
        $model = $this->createSearchModel();
        $model->cate_id = $cate_id;
        $this->render('list', compact('cate', 'model', 'cate_id'));
    }
    
    protected function createSearchModel(){
        $model = new Product('search');
        $model->unsetAttributes();
        if(isset($_GET['Product'])){
            $model->attributes = $_GET['Product'];
        }
        return $model;
    }
    
    public function actionPopup($buy=0){
        $model = $this->createSearchModel();
        if ($buy){
            if (!ErpPrivilege::buyCheck(ErpPrivilege::BUY_ADMIN)){
                $model->assignTo(Yii::app()->user->id);
            }
        }
        $this->renderPartial('popup', array('model' => $model), false, true);
    }
    
    public function actionCreate($cate_id=0){
        $product = new Product;
        $houses = Storehouse::model()->findAll(array('index' => 'id'));
        if(isset($_POST['Product'])){
            $product->attributes=$_POST['Product'];
            if(isset($_POST['attach']['path'][0])){
                $product->photo = $_POST['attach']['path'][0];
            }
            $product->detail->attributes=$_POST['ProductDetail'];
            $path = isset($_POST['attach']['path']) && $_POST['attach']['path'] ? end($_POST['attach']['path']) : '';
            $product->photo = $path;
            $transaction = $product->getDbConnection()->beginTransaction();
            if($product->save()){
                //$product->photo->saveAs($this->module->assetsUrl);
                $product->refresh();
                $data = $_POST['Stock'];
                foreach ($data as $k => $v){
                    if (isset($houses[$v['storehouse_id']])){
                        $house = $houses[$v['storehouse_id']];
                        if($house!=null){
                            $house->newStock($product->id, $v['quantity']);
                        }
                    }
                }
                $transaction->commit();
                if (Yii::app()->request->isAjaxRequest){
                    Yii::app()->end(CJSON::encode(array('status' => 1, 'data' => array('product' => $product->attributes, 'cate' => $product->cate->attributes, 'brand' => $product->brand->attributes, 'unit' => $product->unit->attributes))));
                }else{
                    $this->redirect(array('list'));
                }
            }else{
                $transaction->rollback();
            }
        }
        if($cate_id != '0'){
            $product->cate_id = $cate_id;
        }
        $this->renderPartial('index',array('model'=>$product, 'stock' => new Stock, 'houses' => $houses), false, true);
    }
    
    public function actionUpdate(){
        $product=$this->loadProduct();
        if(isset($_POST['Product'])){
            $product->attributes=$_POST['Product'];
            if($_POST['attach']['path'][0]){
                $product->photo = $_POST['attach']['path'][0];
            }
            $product->detail->attributes=$_POST['ProductDetail'];
            if($product->save()){
                $this->redirect(array('list'));
            }
        }
        $this->renderPartial('index',array('model'=>$product), false, true);
    }
    
    public function actionDelete($id){
        Product::model()->findByPk($id)->delete();
        echo CJSON::encode(array('status' => 1));
    }
    
    protected function loadProduct(){
        return $this->loadModel($_GET['id'], 'Product');
    }
    
    //产品类别添加、修改
    public function actionCate($id=0){
        $model = $id > 0 ? ProductCate::model()->findByPk($id) : new ProductCate;
        
        if(isset($_POST['ProductCate'])){
            $model->attributes=$_POST['ProductCate'];
            
            if($model->save()) {
                if(Yii::app()->request->isAjaxRequest){
                    Yii::app()->end(CJSON::encode(array('status'=>1, 'data'=>array('id'=>$model->id, 'name'=>$model->name))));
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'操作成功')));
                    $this->redirect(Yii::app()->request->urlReferrer);
                }
            }
        }
        
        $this->renderPartial('cate',array('model'=>$model), false, true);
    }
    
    //产品品牌新增、修改
    public function actionBrand($id=0){
        
        $model = $id > 0 ? ProductBrand::model()->findByPk($id) : new ProductBrand;
        
        if(isset($_POST['ProductBrand'])){
        $model->attributes=$_POST['ProductBrand'];
            if($model->save()) {
                if(Yii::app()->request->isAjaxRequest){
                    Yii::app()->end(CJSON::encode(array('status'=>1, 'data'=>array('id'=>$model->id, 'name'=>$model->name))));
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'操作成功')));
                    $this->redirect(Yii::app()->request->urlReferrer);
                }
            }
        }
        $this->renderPartial('brand',array('model'=>$model), false, true);
    }
    
    //产品单位新增、修改
    public function actionUnit($id=0)
    {
        $model = $id > 0 ? ProductUnit::model()->findByPk($id) : new ProductUnit;
        
        if(isset($_POST['ProductUnit'])){
            $model->attributes=$_POST['ProductUnit'];
            if($model->save()) {
                if(Yii::app()->request->isAjaxRequest){
                    Yii::app()->end(CJSON::encode(array('status'=>1, 'data'=>array('id'=>$model->id, 'name'=>$model->name))));
                }else{
                    Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>'操作成功')));
                    $this->redirect(Yii::app()->request->urlReferrer);
                }
            }
        }
        $this->renderPartial('unit',array('model'=>$model), false, true);
    }
    
    
    public function actionCateList($cate_id){
        $data = array(array('', '选择产品'));
        foreach (ProductCate::model()->findByPk($cate_id)->products as $product){
            $data[] = array($product->id, $product->name);
        }
        Yii::app()->end(CJSON::encode($data));
    }
    
    public function actionGridView(){
        $product = new Product('search');
        $product->id = $_GET['id'];
        $dataProvider = $product->search();
        $dataProvider->pagination = false;
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'order-product-grid',
            'dataProvider'=> $dataProvider,
            'selectableRows'=>0,
            'htmlOptions'=>array('style'=>'width:900px'),
            'enablePagination'=>false,
            'emptyText'=>'无产品信息',
            'columns'=>array(
                array('name' => '编号', 'value' => '$data->id'),
                array('name' => '产品名称', 'value' => '$data->name'),
                array('name' => '型号', 'value' => '$data->no'),
                array('name' => '品牌', 'value' => '$data->brand->name'),
                array('name' => '类别', 'value' => '$data->cate->name'),
                array('name' => '单位', 'value' => '$data->unit->name'),
                array('name' => '数量', 'value' => '0'),
                array('name' => '单价（元）', 'value' => '$data->sales_price'),
                array('name' => '金额（元）', 'value' => '0.00'),
                array('name' => '操作', 'value' => 'CHtml::link("删除", "#")'),
            )
        ));
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
