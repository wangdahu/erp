<?php
/**
 * 工作流任务表
 * @author ben
 *
 */
class FlowTask{
    
    public $_table = "core_flow_task";
    
    /**
     * 初始化task
     * @param Array $task_set
     */
    public static function initFlowTask($task_set){
        Yii::app()->db->createCommand()->insert("core_flow_task", $task_set);
        return Yii::app()->db->lastInsertID;
    }
    
    /**
     * 根据任务ID获取任务记录
     * @param int $task_id 任务ID
     * @return int
     */
    public static function getTaskInfoById($task_id) {
        if (!is_numeric($task_id)) {
            return PssFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->from('core_flow_task')->where('id = :id', array(':id' => $task_id))->queryRow();
    }
    
    /**
     * 根据任务ID获取流程名称，发起人UID，表单名称
     * @param int $task_id 任务ID
     * @return array
     */
    public static function getNoticeData($task_id) {
        if (is_numeric($task_id)) {
//            return Yii::app()->db->createCommand()
//                    ->select('cft.user_id,cft.sms_notice,cft.created,cf.name AS flow_name,af.name AS form_name,cf.id AS flow_id,af.tag,
//                              af.sms_content,afrd.json_data')
//                    ->from('core_flow_task cft')
//                    ->join('core_flow cf', 'cf.id = cft.flow_id')
//                    ->join('approve_form_rel afr', 'afr.flow_id = cft.flow_id')
//                    ->join('approve_form af', 'af.id = afr.form_id')
//                    ->join('approve_form_record afrd', 'afrd.task_id = cft.id')
//                    ->where('cft.id = :task_id', array(':task_id' => $task_id))
//                    ->queryRow();
            $result = Yii::app()->db->createCommand()
                    ->select("pff.form_name,cft.sms_notice,cf.id as flow_id,cft.user_id,
                             cft.created,cf.name as flow_name,pff.form_name as sms_content,pff.form_name as json_data")
                    ->from('pss_form_flow pff')
                    ->join('core_flow cf', 'cf.id=pff.flow_id')
                    ->join('core_flow_node cfn', 'cfn.flow_id=cf.id')
                    ->join('core_flow_task cft', 'cft.flow_id=cf.id')
                    ->where('cft.id = :task_id', array(':task_id' => $task_id))
                    ->queryRow();
            return $result;
        } else {
            return PssFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 根据流程ID获取正在审批的任务
     * @param int $flow_id
     */
    public static function getTaskListByFlowId($flow_id) {
        if (!is_numeric($flow_id)) {
            return PssFlow::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->from('core_flow_task')->where('flow_id = :flow_id', array(':flow_id' => $flow_id))->queryAll();
        }
    }
    
    /**
     * 获取任务状态
     * @param int $task_id 任务ID
     * @return int
     */
    public static function getTaskStatus($task_id) {
        if (!is_numeric($task_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->select('status')->from('core_flow_task')->where('id = :task_id', array(':task_id' => $task_id))->queryScalar();
    }
    
    /**
     * 更新审批流程状态
     * @param int $task_id 任务ID
     * @param int $status 任务状态，1，流转中，2，通过，3，不通过，4，取消
     * @return boolean
     */
    public static function updateTaskStatus($task_id, $status) {
        if (!is_numeric($task_id) || !is_numeric($status)) {
            return PssFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_task', array('status' => $status), "id = :id", array(':id' => $task_id));
    }
}