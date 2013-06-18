<?php 
$absolute_path = $this->route;
$path_arr = explode('/', $absolute_path);
unset($path_arr[count($path_arr)-1]);
$absolute_path = implode('/', $path_arr);

$task = $model->task;

?>
<div id="tool_box" style="float:right; margin-top: -30px;">
    <?php
        $_user_id = Yii::app()->user->id;
        $_form = Yii::app()->db->createCommand()->from("core_flow_task")->where("id = :task_id",array(":task_id" => $model->approval_id))->queryRow();
        
         if ($_user_id == $_form['user_id'] &&!$task->getIsProcessing()) {
             echo CHtml::link("修改单据", Yii::app()->createUrl("/".$absolute_path."/update", array("id" => $model->id)), array("class" => "button"));
         }
         echo "&nbsp;";
         if ($_user_id == $_form['user_id'] && !$task->getIsComplete()) {
             $task_model = CoreFlowTask::model()->findByPk($model->approval_id);
             echo CHtml::link("撤消单据", Yii::app()->createUrl("/pss/approve/repealFlowForm", array("id" => $model->id, "flow_id" => $task_model->flow_id, "task_id" => $model->approval_id, "status" => 4, 'absolute_path' => $absolute_path, 'class_name' => get_class($model))), array("class" => "button js-dialog-link"));
         }
//         echo "&nbsp;";
//         if (1){//$task->getIsPass() && $task->lastNode->isAssigned(Yii::app()->user->id, $task)) {
//             echo CHtml::link("作废单据", Yii::app()->createUrl("/approve/default/repealFlowForm", array("flow_id" => 1, "task_id" => 1, "status" => 5)), array("class" => "button js-dialog-link highlight"));
//         }
//         echo "&nbsp;";
     ?>
</div>