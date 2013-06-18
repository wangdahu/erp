<?php
/**
 * 审批过程表
 * @author ben
 *
 */
class FlowProcess{
    /**
     * 初始化Process
     * @param int $task_id 任务ID
     * @param string $flow_id 流程ID
     * @param string $node_id 节点ID
     * @return boolean
     */
    public static function initFlowProcess($task_id, $node_id) {
        //初始化节点
        $date = date('Y-m-d H:i:s');
        $process_set = array(
            'task_id' => $task_id,
            'node_id' => $node_id,
            'status' => 1,
            'created' => $date,
        );
        return Yii::app()->db->createCommand()->insert('core_flow_process', $process_set);
    }
    
    /**
     * 修改节点状态
     * @param int $node_id
     * @param int $task_id
     * @param int $current_node  下一节点
     */
    public static function updateFlowProcess($node_id, $task_id, $current_node){
        $param = array(':task_id' => $task_id);
        $where = "task_id = :task_id AND node_id = {$current_node}";
        Yii::app()->db->createCommand()->update('core_flow_process', array('node_id' => $node_id), $where, $param);
        return 1;
    }
    
    /**
     * 获取任务节点状态
     * @param int $task_id 任务ID
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getNodeStatus($task_id, $node_id) {
        $param = array(':task_id' => $task_id, ':node_id' => $node_id);
        return Yii::app()->db->createCommand()->select('status')->from('core_flow_process')
                        ->where('task_id = :task_id AND node_id = :node_id', $param)->queryScalar();
    }
    
    /**
     * 更新审批流程节点状态
     * @param int $task_id 任务ID
     * @param int $node_id 节点ID
     * @param int $status 任务状态，1，流转中，2，通过，3，不通过，4，取消
     * @return boolean
     */
    public static function updateNodeStatus($task_id, $node_id, $status) {
        if (!is_numeric($task_id) || !is_numeric($status) || !is_numeric($node_id)) {
            return PssFlow::PARSE_PARAM_ERROR;
        }
        $param = array(':task_id' => $task_id, ':node_id' => $node_id);
        return Yii::app()->db->createCommand()->update('core_flow_process', array('status' => $status), "task_id = :task_id AND node_id = :node_id", $param);
    }
    
    /**
     * 获取任务的当前节点
     * @param int $task_id 任务ID
     * @return int
     */
    public static function getCurrentNode($task_id) {
        if (is_numeric($task_id)) {
            $sql = "SELECT node_id FROM core_flow_process WHERE task_id = :task_id ORDER BY id DESC LIMIT 1";
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':task_id' => $task_id));
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }
}