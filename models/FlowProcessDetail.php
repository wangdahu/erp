<?php
/**
 * 审批过程表
 * @author ben
 *
 */
class FlowProcessDetail{
    /**
     * 根据TASK ID 获取已经审批的节点关联对象ID数据
     * @param int $task_id 任务ID
     * @return array
     */
    public static function getApprovedRelatesByTaskId($task_id) {
        if (is_numeric($task_id)) {
            return Yii::app()->db->createCommand()->select('node_relate_id')->from('core_flow_process_detail')
                            ->where('task_id = :task_id', array(':task_id' => $task_id))->queryColumn();
        } else {
            return PssFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 获取审批记录
     * @param int $task_id 任务ID
     * @return array
     */
    public static function getFlowProcessDetailByTaskId($task_id){
        return Yii::app()->db->createCommand()->from('core_flow_process_detail cfpd')
                         ->join('core_flow_node cfn', 'cfn.id = cfpd.node_id')
                         ->where('task_id = :task_id', array(':task_id' => $task_id))
                         ->queryAll();
    }
    
    /**
     * 初始化ProcessDetail
     * @param array $param 记录审批明细
     * @return boolean
     */
    public static function insertFlowProcessDetail($param) {
        return Yii::app()->db->createCommand()->insert('core_flow_process_detail', $param);
    }
    
    /**
     * 获取参与流程审批的所有人员
     * @param int $task_id
     * @return array
     */
    public static function getTaskUserList($task_id) {
        if (is_numeric($task_id)) {
            $sql = "SELECT approved_user_id FROM core_flow_process_detail WHERE task_id = :task_id";
            return Yii::app()->db->createCommand($sql)->queryColumn(array(':task_id' => $task_id));
        } else {
            return PssFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 审批明细记录中节点下的关联对象数量
     * @param int $node_id
     * @param int$task_id
     */
    public static function taskCount($node_id, $task_id){
        return Yii::app()->db->createCommand('SELECT count(id) FROM core_flow_process_detail WHERE node_id = :node_id AND task_id = :task_id')
                    ->queryScalar(array(':node_id' => $node_id, ':task_id' => $task_id));
    }
}