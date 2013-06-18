<?php

class CommonController extends PssController
{
	
    /**
     * 获取城市列表
     */
    public function actionDistrict($pid = 0){
        $options = District::getChildrens($pid);
        $data = array(array('', '请选择城市'));
        foreach ($options as $k => $v){
            $data[] = array($k, $v);
        }
        echo json_encode($data);
        Yii::app()->end();
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