<?php
class Flow{
    
    /**
     * 新增审批流程 参数数组
     * @param Array $param
     * @return int lastInsertID
     */
    public static function insertFlow($param){
        Yii::app()->db->createCommand()->insert('core_flow', $param);
        return Yii::app()->db->lastInsertID;
    }
    
    /**
     * 根据ID更新审批流程表
     * @param array $param 更新参数数组
     * @param $group_id 审批流程ID
     * @return array
     */
    public static function updateFlowById($param, $flow_id) {
        if (empty($param) && !is_array($param) && !is_numeric($flow_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow', $param, 'id = :id', array(':id' => $flow_id));
    }
    
    /**
     * 根据流程ID返回类型ID
     * @param int $flow_id 流程ID
     * @return $group_id
     */
    public static function getGroupIdByFlowId($flow_id) {
        if (!is_numeric($flow_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->select('group_id')->from('core_flow')
                            ->where('id = :flow_id AND deleted = 0', array(':flow_id' => $flow_id))->queryScalar();
        }
    }
    
    /**
     * 将流程置为历史
     * @param int $flow_id 流程ID
     * @return boolean
     */
    public static function historyFlow($flow_id) {
        if (!is_numeric($flow_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->update('core_flow', array('is_history' => 1), 'id = :flow_id', array(':flow_id' => $flow_id));
        }
    }
    
    /**
     * 根据标签获取审批流程
     * @param int $flow_id 审批流程ID
     * @return array
     */
    public static function getFlowById($flow_id) {
        if (empty($flow_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->from('core_flow')->where('deleted = 0 AND id = :flow_id', array(':flow_id' => $flow_id))->queryRow();
    }
    
}
