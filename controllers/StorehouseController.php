<?php

class StorehouseController extends ErpController
{
    

    
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
/* 	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	} */

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}
	
	public function saveStorehouse($model, $notice){
	    $this->performAjaxValidation($model);
        
        if(isset($_POST['Storehouse']))
        {
            $model->attributes=$_POST['Storehouse'];
            if($model->save()){
                Yii::app()->user->setFlash('page_flash', json_encode(array('msg'=>$notice)));
                $this->redirect(Yii::app()->request->urlReferrer);
            }
        }
        $this->renderPartial('form', array('model'=>$model), false, true);
	}

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(){
        $model=new Storehouse;
        // Uncomment the following line if AJAX validation is needed
        $this->saveStorehouse($model, '新增成功');
    }
    
    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id){
        $model=$this->loadModel($id, 'Storehouse');
        // Uncomment the following line if AJAX validation is needed
        $this->saveStorehouse($model, '保存成功');
    }

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = Storehouse::model()->findByPk($id);
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if($model->delete()){
			    echo CJSON::encode(array('status' => 1));
			}else{
			    echo CJSON::encode(array('status' => 0, 'msg' => $model->getError('stocks')));
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Storehouse');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Storehouse('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Storehouse']))
			$model->attributes=$_GET['Storehouse'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}


	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='storehouse-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
