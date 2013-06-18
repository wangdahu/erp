<?php
/**
 * 节点关联对象表
 * @author ben
 *
 */
class FlowUserBind{

    /**
     * 获取当前用户绑定的部门
     * @param $uid 用户ID
     * @return array
     */
    public static function bindDeptList($uid) {
        if (!is_numeric($uid)) {
            return ErpFlow::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->select('dept_id')->from('core_flow_user_bind')->where('uid = :uid', array(':uid' => $uid))->queryAll();
    }
}
