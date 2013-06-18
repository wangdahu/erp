<?php

class SettingController extends PssController{
    
    
    public $defaultAction = 'storehouse';
    
    protected $tabViewData = array();
    
    public function init(){
        if (!PssPrivilege::otherCheck(PssPrivilege::SETTING)){
            throw new CHttpException(403, '无权限查看 <a href="javascript:" onclick="history.go(-1); ">返回上一步</a>');
        }
        parent::init();
        $this->breadcrumbs['设置'] = array('/pss/setting');
    }
    
	public function actionStorehouse(){
	    $this->breadcrumbs[] = '仓库设置';
        $model = new Storehouse('search');
        $model->unsetAttributes();
        if(isset($_GET[get_class($model)])){
            $model->attributes=$_GET[get_class($model)];
        }
		$this->render('storehouse', array('model' => $model));
	}
	
	public function actionProduct($type=1){
	    $this->breadcrumbs[] = '产品设置';
        $model = $this->createModel($type);
		$this->render('product', array('model' => $model, 'type' => $type));
	}
	
    public function actionDelete(){
        if (isset($_GET['id'], $_GET['type'])){
            switch ($_GET['type']){
                case '1':
                    $model = ProductCate::model()->findByPk($_GET['id']);
                    break;
                case '2':
                    $model = ProductBrand::model()->findByPk($_GET['id']);
                    break;
                case '3':
                    $model = ProductUnit::model()->findByPk($_GET['id']);
                    break;
            }
			if($model->delete()){
			    echo CJSON::encode(array('status' => 1));
			}else{
			    echo CJSON::encode(array('status' => 0, 'msg' => $model->getError('products')));
			}
        }
    }
	
	protected function createModel($type){
	    $request = Yii::app()->request;
	    switch ($type){
	        case '1':
	            $model = new ProductCate('search');
	            break;
	        case '2':
	            $model = new ProductBrand('search');
	            break;
	        case '3':
	            $model = new ProductUnit('search');
	            break;
	    }
	    $model->unsetAttributes();  // clear any default values
	    if(isset($_GET[get_class($model)])){
	        $model->attributes=$_GET[get_class($model)];
	    }
	    return $model;
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