<?php
/**
 * 节点关联对象表
 * @author Administrator
 *
 */
class FlowNodeRelate {
    /**
     * 根据节点ID获取相应关联角色
     * @param $node_id 节点ID
     * @return array
     */
    public static function getRelateByNodeId($node_id) {
        if (is_numeric($node_id)) {
            return Yii::app()->db->createCommand()->from('core_flow_node_relate')->where('node_id = :node_id AND deleted = 0', array(':node_id' => $node_id))->queryAll();
        } else {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 根据节点ID删除节点关联角色
     * @param int $node_id 节点ID
     * @return array
     */
    public static function delRelateByNodeId($node_id) {
        if (!is_numeric($node_id)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_node_relate', array('deleted' => 1), 'node_id = :node_id', array(':node_id' => $node_id));
    }
    
    /**
     * 
     * 节点下的关联对象数量
     * @param int $node_id
     */
    public static function relateCounts($node_id){
        return Yii::app()->db->createCommand('SELECT count(id) FROM core_flow_node_relate WHERE node_id = :node_id AND deleted = 0')
                    ->queryScalar(array(':node_id' => $node_id));
    }
    
    /**
     * 新增节点关联角色
     * @param array $param 关联角色 参数数组
     * @return array
     */
    public static function insertRelate($param) {
        if (empty($param) && !is_array($param)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        Yii::app()->db->createCommand()->insert('core_flow_node_relate', $param);
        return Yii::app()->db->lastInsertID;
    }
}
