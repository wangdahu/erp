<?php
/**
 * 工作流节点
 * @author ben
 *
 */
class FlowNode{
    /**
     * 获取流程的结束完成通知节点
     * @param int $flow_id
     * @return int
     */
    public static function getEndNode($flow_id) {
        if (is_numeric($flow_id)) {
            $sql = 'SELECT MAX(id) FROM core_flow_node WHERE flow_id = :flow_id AND deleted = 0 AND type = 2';
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':flow_id' => $flow_id));
        } else {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 获取流程的下一节点
     * @param int $task_id 任务ID
     * @return int
     */
    public static function getNextNode($task_id, $current_node) {
        if (!is_numeric($task_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        $flow_id = self::getFlowIdByNode($current_node);
        if (!is_numeric($flow_id)) {
            return ErpFlow::GET_CURRENT_FlOW_ERROR;
        }
        $sql = "SELECT id FROM core_flow_node WHERE flow_id = :flow_id AND id > :id AND deleted = 0 AND type = 0 LIMIT 1";
        return Yii::app()->db->createCommand($sql)->queryScalar(array(':flow_id' => $flow_id, ':id' => $current_node));
    }
    
    /**
     * 跟进流程ID获取审批流程所有节点 不包含启动审批人 和 完成审批节点
     * @param $flow_id 流程ID
     * @return array
     */
    public static function getNormalNodeByFlowId($flow_id) {
        if (is_numeric($flow_id)) {
            return Yii::app()->db->createCommand()->from('core_flow_node')
                            ->where('flow_id = :flow_id AND deleted = 0 AND type = 0', array(':flow_id' => $flow_id))->queryAll();
        } else {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 根据节点ID删除审批流程节点
     * @param string $node_id 节点ID
     * @return array
     */
    public static function delNodeById($node_id) {
        if (!is_numeric($node_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_node', array('deleted' => 1), 'id = :node_id', array(':node_id' => $node_id));
    }
    
    /**
     * 新增审批流程节点
     * @param array $param 新增审批流程节点 参数数组
     * @return array
     */
    public static function insertNode($param) {
        if (empty($param) && !is_array($param)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        Yii::app()->db->createCommand()->insert('core_flow_node', $param);
        return Yii::app()->db->lastInsertID;
    }
    
    /**
     * 判断节点是否需要全部通过
     * @param int $node_id 节点ID
     * @return boolean
     */
    public static function isAllAllowed($node_id) {
        if (is_numeric($node_id)) {
            return Yii::app()->db->createCommand('SELECT need_all_allowed FROM core_flow_node WHERE id = :node_id AND deleted = 0')
                            ->queryScalar(array(':node_id' => $node_id));
        } else {
            return false;
        }
    }
    
    /**
     * 根据节点ID获取流程ID
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getFlowIdByNode($node_id) {
        if (!is_numeric($node_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->select('flow_id')->from('core_flow_node')
                        ->where('deleted = 0 AND id = :id', array(':id' => $node_id))->queryScalar();
    }
    
    /**
     * 获取流程的最后一个节点
     * @param int $flow_id
     * @return int
     */
    public static function getLastNode($flow_id) {
        if (is_numeric($flow_id)) {
            $sql = 'SELECT MAX(id) FROM core_flow_node WHERE flow_id = :flow_id AND deleted = 0 AND type = 0';
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':flow_id' => $flow_id));
        } else {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 跟进流程ID获取审批流程所有节点 包含启动审批人 和 完成审批节点
     * @param $flow_id 流程ID 
     * @return array
     */
    public static function getNodeByFlowId($flow_id) {
        if (is_numeric($flow_id)) {
            return Yii::app()->db->createCommand()->from('core_flow_node')
                            ->where('flow_id = :flow_id AND deleted = 0', array(':flow_id' => $flow_id))->queryAll();
        } else {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 判断节点是否可以跳过 直接进入下一节点审批
     * @param int $node_id 节点ID
     * @return boolean
     */
    public static function isIgnored($node_id) {
        if (is_numeric($node_id)) {
            return Yii::app()->db->createCommand('SELECT ignored FROM core_flow_node WHERE id = :node_id AND deleted = 0')->queryScalar(array(':node_id' => $node_id));
        } else {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
    }
    
}
