<?php

/**
 * 工作流接口类
 */
class ErpFlow {
    /**
     * 错误信息常量定义
     */

    const NOT_DEFINE_FLOW_NODE = -1001; //未定义流程节点
    const NOT_DEFINE_FLOW_TAG = -1002; //未定义流程标签
    const TASK_ALREADY_EXIST = -1003; //任务已经存在
    const PARSE_PARAM_ERROR = -1004; //参数解析错误
    const GET_CURRENT_RELATE_ERROR = -1005; //获取当前关联对象错误
    const GET_CURRENT_NODE_ERROR = -1006; //获取当前节点错误
    const GET_CURRENT_FlOW_ERROR = -1007; //获取当前流程错误
    const GET_APPROVED_RECORD_ERROR = -1008; //获取审批记录错误
    const NOT_APPROVED_AUTHORITY = -1009; //没有审批权限
    const INSERT_FLOW_GROUP_ERROR = -1010; //插入流程类型错误
    const NOT_DEFINE_NODE_RELATE = -1011; //未定义节点关联对象
    const NOT_DEFINE_FLOW = -1012; //未定义流程
    const FLOW_NODE_ERROR = -1013; //节点+关联对象不存在 或者 节点关联对象没有对应的UID
    
    const APPROVAL_FOLLOW = 1;//审批流转中
    const APPROVAL_PASS = 2;//审批通过
    const APPROVAL_FAIL = 3;//审批不通过
    
    /**
     * 获取所有审批流程类型列表
     * @return array
     */

    public static function listAllFlowGroup() {
        return Yii::app()->db->createCommand()->from('core_flow_group')->where('deleted = 0')->queryAll();
    }

    /**
     * 获取所有审批表单类型列表
     * @return array
     */
    public static function listAllFormGroup() {
        return Yii::app()->db->createCommand()->from('approve_group')->where('deleted = 0')->queryAll();
    }

    /**
     * 根据标签删除审批流程类型
     * @param string $tag 审批流程类型标签
     * @return array
     */
    public static function delFlowGroupByTag($tag) {
        if (empty($tag)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_group', array('deleted' => 1), 'tag = :tag', array(':tag' => $tag));
    }

    /**
     * 根据标签获取审批流程类型
     * @param string $tag 审批流程类型标签
     * @return array
     */
    public static function getFlowGroupByTag($tag) {
        if (empty($tag)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->from('core_flow_group')->where('deleted = 0 AND tag = :tag', array(':tag' => $tag))->queryRow();
    }
    
    /**
     * 根据节点ID获取相应关联角色
     * @param $node_id 节点ID
     * @return array
     */
    public static function getRelateByNodeId($node_id) {
        return FlowNodeRelate::getRelateByNodeId($node_id);
    }
    
    /**
     * 根据标签获取审批流程类型
     * @param string $tag 审批流程类型标签
     * @return array
     */
    public static function getFlowGroupById($id) {
        if (!is_numeric($id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->from('core_flow_group')->where('deleted = 0 AND id = :id', array(':id' => $id))->queryRow();
    }

    /**
     * 新增审批流程类型
     * @param array $param 新增审批流程类型参数数组
     * @return array
     */
    public static function insertFlowGroup($param) {
        if (empty($param) && !is_array($param)) {
            return self::PARSE_PARAM_ERROR;
        }
        if (Yii::app()->db->createCommand()->insert('core_flow_group', $param)) {
            return Yii::app()->db->lastInsertID;
        } else {
            return false;
        }
    }

    /**
     * 根据ID更新审批流程类型表
     * @param array $param 更新参数数组
     * @param $group_id 审批流程类型ID
     * @return array
     */
    public static function updateFlowGroupById($param, $group_id) {
        if (empty($param) && !is_array($param) && !is_numeric($group_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_group', $param, 'id = :id', array(':id' => $group_id));
    }

    /**
     * 根据流程类型ID获取审批流程列表 包含历史流程
     * @param int $group_id 流程类型ID 
     * @return array
     */
    public static function getFlowByGroupId($group_id) {
        if (is_numeric($group_id)) {
            return Yii::app()->db->createCommand()->from('core_flow')
                            ->where('group_id = :group_id AND deleted = 0', array(':group_id' => $group_id))->queryAll();
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 根据流程类型ID获取正常状态的审批流程列表 不包含历史流程
     * @param int $group_id 流程类型ID
     * @return array
     */
    public static function getNormalFlowByGroupId($group_id) {
        if (is_numeric($group_id)) {
            return Yii::app()->db->createCommand()->select('cf.*,af.name AS form_name')->from('core_flow cf')
                            ->join('approve_form_rel afr', 'cf.id = afr.flow_id')
                            ->join('approve_form af', 'af.id = afr.form_id')
                            ->where('cf.group_id = :group_id AND cf.deleted = 0 AND is_history = 0', array(':group_id' => $group_id))->queryAll();
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 获取所有审批流程列表
     * @return array
     */
    public static function listAllFlow() {
        return Yii::app()->db->createCommand()->from('core_flow')->where('deleted = 0')->queryAll();
    }
    
    /**
     * 获取所有审批流程列表不包含历史流程
     * @param int $group_id
     * @return array
     */
    public static function listAllFlowExceptHistory($group_id=0) {
        return Yii::app()->db->createCommand()->select('cf.id as flow_id,cf.*, pff.*')->from('core_flow AS cf')
                        ->join('erp_form_flow pff', 'pff.flow_id = cf.id')
                        ->where('cf.deleted = 0 AND cf.is_history = 0 AND cf.group_id='.$group_id)
                        ->queryAll();
    }
    
    /**
     * 根据标签删除审批流程
     * @param string $tag 审批流程标签
     * @return array
     */
    public static function delFlowByTag($tag) {
        if (empty($tag)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow', array('deleted' => 1), 'tag = :tag', array(':tag' => $tag));
    }
    
    /**
     * 跟进流程ID获取审批流程所有节点 不包含启动审批人 和 完成审批节点
     * @param $flow_id 流程ID
     * @return array
     */
    public static function getNormalNodeByFlowId($flow_id) {
        return FlowNode::getNormalNodeByFlowId($flow_id);
    }
    
    /**
     * 新增审批流程
     * @param array $param 新增审批流程 参数数组
     * @return boolean
     */
    public static function insertFlow($param) {
        if (empty($param) && !is_array($param)) {
            return self::PARSE_PARAM_ERROR;
        }
        $lastInsertId = Flow::insertFlow($param);
        if ($lastInsertId) {
            return $lastInsertId;
        } else {
            return false;
        }
    }
        
    /**
     * 新建表单和流程关系
     * @param int $flow_id 流程ID
     * @param int $form_id 表单ID
     */
    public static function insertFlowFormRel($flow_id, $form_name) {
        Yii::app()->db->createCommand()->insert('erp_form_flow', array('flow_id' => $flow_id, 'form_name' => $form_name, 'deleted' => 0));
        return Yii::app()->db->lastInsertID;
    }
    
    /**
     * 获取树形结构以及相关数据
     * @param int $group_id 表单类型ID
     * @param int $form_id 表单ID
     * @param int $flow_id 流程ID
     * @param int $flag 是否获取表单对应流程 1为是 0为否
     * @return array
     */
     public static function getData($group_id, $flag = 0){
        // 取得所表单类型、表单
        $current_group = $current_form = array();
        //$groups = ErpFlow::listAllFormGroup();
        $groups = array(array('id'=>'1', 'name'=>'销售单'), 
                       array('id'=>'2', 'name'=>'采购单'), 
                       array('id'=>'3', 'name'=>'入库单'), 
                       array('id'=>'4', 'name'=>'出库单'), 
                       array('id'=>'5', 'name'=>'调拨单'), 
                       array('id'=>'6', 'name'=>'销售退货单'), 
                       array('id'=>'7', 'name'=>'采购退货单'),
                 );
        if(empty ($group_id)){
            $group_id = $groups[0]['id'];
        }
        $data = array();
        // 取得审批流程树形菜单
        if(count($groups)){
            //审批类型
            foreach ($groups as $group) {
                if($group_id == $group['id']){
                    $current_group = $group;
                }
                $data[] = array(
                        'text' => Html5::link($group['name'], array('/erp/approveflow/index&type=1', 'group_id' => $group['id']), array('class' => $group_id == $group['id'] ? 'selected' : '',)),
                        'htmlOptions' => array('data-id' => $group['id'], 'class' => $group['id'] == $group_id ? 'active' : ''),
                        'id' => 'group_' . $group['id'],
                        'parent_id' => 0,
                );
                $forms = ErpFlow::listAllFlowForm($group['id']);
//                if(count($forms)){
//                    //审批流程
//                    foreach ($forms as $k => $form) {
//                        if(empty ($form_id) && $k == 0 && $group_id == $group['id']){
//                            $form_id = $form['id'];
//                        }
//                        if($form_id == $form['id']){
//                            $current_form = $form;
//                        }
//                        $data[] = array(
//                                'text' => Html5::link($form['name'], array('/erp/approveflow/index&type=1', 'group_id' => $group['id'], 'form_id' => $form['id']),
//                                        array('class' => $form_id == $form['id'] ? 'selected' : '',)),
//                                'htmlOptions' => array('data-id' => $form['id'], 'class' => $form['id'] == $form_id ? 'active' : ''),
//                                'id' => 'form_' . $form['id'],
//                                'parent_id' =>  'group_' . $group['id'],
//                        );
//                    }
//                }
            }
        }
        $tree = Tree::treeView($data);
        $result = array('tree' => $tree, 'current_form' => $current_form, 'current_group' => $current_group);
        if($flag){
            //$result['flows'] = self::getFlowsByFormId($form_id);
        }
        return $result;
    }

    /**
     * 根据标签获取审批流程
     * @param string $tag 审批流程标签
     * @return array
     */
    public static function getFlowByTag($tag) {
        if (empty($tag)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->from('core_flow')->where('deleted = 0 AND tag = :tag', array(':tag' => $tag))->queryRow();
    }

    /**
     * 根据标签获取审批流程
     * @param int $flow_id 审批流程ID
     * @return array
     */
    public static function getFlowById($flow_id) {
        return Flow::getFlowById($flow_id);
    }

    /**
     * 根据ID更新审批流程表
     * @param array $param 更新参数数组
     * @param $group_id 审批流程ID
     * @return array
     */
    public static function updateFlowById($param, $flow_id) {
        return Flow::updateFlowById($param, $flow_id);
    }

    /**
     * 验证流程是否创建节点
     * @param string $tag 流程标签
     * @return boolean
     */
    public static function validateFlowNode($tag) {
        $row = self::getFlowByTag($tag);
        if (empty($row)) {
            return self::PARSE_PARAM_ERROR;
        }
        $node_id = self::getFirstNode($row['id']);
        return empty($node_id) ? false : true;
    }

    /**
     * 获取审批流程所有节点
     * @return array
     */
    public static function listAllNode() {
        return Yii::app()->db->createCommand()->from('core_flow_node')->where('deleted = 0 AND type = 0')->queryAll();
    }
    
    /**
     * 获取用户 角色 部门已选ID数组
     * @param string $user
     * @param string $role
     * @param string $department
     * @return array
     */
     public static function getSelectedList($user, $role, $department, $department_manage = ''){
        $is_create = false;
        $user_list = $role_list = $department_list = $department_manage_list = array();
        if(empty($user) && empty($role) && empty($department) && empty($department_manage)){
            $is_create = true;
        }else{
            if(!empty($user)){
                $user_list = explode(',', $user);
            }
            if(!empty($role)){
                $role_list = explode(',', $role);
            }
            if(!empty($department)){
                $department_list = explode(',', $department);
            }
            if(!empty($department_manage)){
                $department_manage_list = explode(',', $department_manage);
            }
        }
        Yii::app()->clientScript->scriptMap['jquery.js'] = false;
        return array('department_manage_list' => $department_manage_list, 'user_list' => $user_list, 'role_list' => $role_list, 'department_list' => $department_list, 'is_create' => $is_create);
    }
    
    /**
     * 跟进流程标签获取审批流程所有节点
     * @param $flow_tag 流程标签
     * @return array
     */
    public static function getNodeByFlowTag($flow_tag) {
        if (empty($flow_tag)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            $row = self::getFlowByTag($flow_tag);
            if (empty($row)) {
                return self::PARSE_PARAM_ERROR;
            }
            return Yii::app()->db->createCommand()->from('core_flow_node')->where('flow_id = :flow_id AND deleted = 0', array(':flow_id' => $row['id']))->queryAll();
        }
    }

    /**
     * 根据节点ID获取节点信息
     * @param $node_id 节点ID
     * @return array
     */
    public static function getNodeById($node_id) {
        if (is_numeric($node_id)) {
            return Yii::app()->db->createCommand()->from('core_flow_node')->where('id = :id AND deleted = 0', array(':id' => $node_id))->queryRow();
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 根据流程标签删除审批流程节点
     * @param string $tag 审批流程标签
     * @return array
     */
    public static function delNodeByFlowTag($tag) {
        if (empty($tag)) {
            return self::PARSE_PARAM_ERROR;
        }
        $row = self::getFlowByTag($tag);
        if (empty($row)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_node', array('deleted' => 1), 'flow_id = :flow_id', array(':flow_id' => $row['id']));
    }
    
    /**
     * 新增审批流程节点
     * @param array $param 新增审批流程节点 参数数组
     * @return array
     */
    public static function insertNode($param) {
        return FlowNode::insertNode($param);
    }

    /**
     * 根据ID更新审批流程表
     * @param array $param 更新参数数组
     * @param $group_id 审批流程ID
     * @return array
     */
    public static function updateNodeById($param, $node_id) {
        if (empty($param) && !is_array($param) && !is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_node', $param, 'id = :id', array(':id' => $node_id));
    }

    /**
     * 获取流程的第一个节点
     * @param int $flow_id 流程ID
     * @return int
     */
    public static function getFirstNode($flow_id) {
        if (is_numeric($flow_id)) {
            $sql = "SELECT MIN(id) FROM core_flow_node WHERE flow_id = :flow_id AND deleted = 0 AND type = 0 ";
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':flow_id' => $flow_id));
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 获取流程的启动节点
     * @param int $flow_id 流程ID
     * @return int
     */
    public static function getStartNode($flow_id) {
        if (is_numeric($flow_id)) {
            $sql = "SELECT MIN(id) FROM core_flow_node WHERE flow_id = :flow_id AND deleted = 0 AND type = 1";
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':flow_id' => $flow_id));
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 判断节点是否是流程的最后一个节点
     * @param int $node_id 节点ID
     * @return boolean
     */
    public static function isLastNode($node_id) {
        if (is_numeric($node_id)) {
            $flow_id = FlowNode::getFlowIdByNode($node_id);
            $last_node_id = FlowNode::getLastNode($flow_id);
            return $node_id == $last_node_id ? true : false;
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 检查节点是否下的用户是否已经全部审批完成
     * @param int $node_id 节点ID
     * @param int $task_id 任务ID
     * @return boolean
     */
    public static function checkNodeIsAllApproved($node_id, $task_id) {
        if (is_numeric($node_id) && is_numeric($task_id)) {
            //节点下的关联对象数量
            $relate_count = FlowNodeRelate::relateCounts($node_id);
            //审批明细记录中节点下的关联对象数量
            $task_count = FlowProcessDetail::taskCount($node_id, $task_id);
            if ($relate_count == $task_count) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception(self::PARSE_PARAM_ERROR);
        }
    }
    
    /**
     * 根据关联ID获取相应关联角色
     * @param $node_id 节点ID
     * @return array
     */
    public static function getRelateById($id) {
        if (is_numeric($id)) {
            return Yii::app()->db->createCommand()->from('core_flow_node_relate')->where('id = :id AND deleted = 0', array(':id' => $id))->queryRow();
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 根据主键删除节点关联角色
     * @param int $id 审批流程标签
     * @return array
     */
    public static function delRelateById($id) {
        if (!is_numeric($id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_node_relate', array('deleted' => 1), 'id = :id', array(':id' => $id));
    }
    
    /**
     * 添加节点时 处理关联对象的数据
     * @param int $node_id 节点ID
     * @param string $user 用户ID 字符串
     * @param string $role 角色ID 字符串
     * @return array
     */
    public static function processRelateData($node_id, $user = '', $role = '') {
        if (!is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $param = array('node_id' => $node_id, 'deleted' => 0);
        if (!empty($user)) {
            $user_list = explode(',', $user);
            if (count($user_list)) {
                $param['type'] = 2;
                foreach ($user_list as $uid) {
                    $param['relate_id'] = $uid;
                    FlowNodeRelate::insertRelate($param);
                }
            }
        }
        if (!empty($role)) {
            $role_list = explode(',', $role);
            if (count($role_list)) {
                $param['type'] = 1;
                foreach ($role_list as $role_id) {
                    $param['relate_id'] = $role_id;
                    FlowNodeRelate::insertRelate($param);
                }
            }
        }
        return true;
    }

    /**
     * 根据ID更新审批流程表
     * @param array $param 更新参数数组
     * @param $id 关联角色主键ID
     * @return array
     */
    public static function updateRelateById($param, $id) {
        if (empty($param) && !is_array($param) && !is_numeric($id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->update('core_flow_node_relate', $param, 'id = :id', array(':id' => $id));
    }

    /**
     * 绑定部门
     * @param $uid 用户ID
     * @param $dept 部门数组
     * @return boolean
     */
    public static function bindDept($uid, $dept) {
        if (is_array($dept) || is_numeric($dept)) {
            $transaction = YII::app()->db->beginTransaction();
            try {
                Yii::app()->db->createCommand()->delete('core_flow_user_bind', 'uid = :uid', array(':uid' => $uid));
                if (is_array($dept)) {
                    if (count($dept)) {
                        foreach ($dept as $v) {
                            Yii::app()->db->createCommand()->insert('core_flow_user_bind', array('uid' => $uid, 'dept_id' => $v));
                        }
                    }
                } else {
                    Yii::app()->db->createCommand()->insert('core_flow_user_bind', array('uid' => $uid, 'dept_id' => $dept));
                }
                $transaction->commit();
                return true;
            } catch (Exception $exc) {
                $transaction->rollback();
                return false;
            }
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 验证流程是否已经产生任务
     * @param int $flow_id 流程ID
     * @return boolean
     */
    public static function isExistTask($flow_id) {
        if (!is_numeric($flow_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $res = FlowTask::getTaskListByFlowId($flow_id);
        if (empty($res) || !is_array($res)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取任务ID
     * @param int $flow_id 关联ID
     * @param string $type 关联类型
     * @return int
     */
    public static function getTaskId($relate_id, $type) {
        if (!is_numeric($relate_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $param = array(':relate_id' => $relate_id, ':type' => $type);
        return Yii::app()->db->createCommand()->select('id')->from('core_flow_task')->where('relate_id = :relate_id AND type = :type', $param)->queryScalar();
    }
    
    /**
     * 任务是否已完成审批
     * @param int $task_id
     */
    public static function isTaskComplate($task_id) {
        return FlowTask::getTaskStatus($task_id) != 1;
    }
    
    /**
     * 获取任务节点状态
     * @param int $task_id 任务ID
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getNodeStatus($task_id, $node_id) {
        if (!is_numeric($task_id) || !is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return FlowProcess::getNodeStatus($task_id, $node_id);
    }
    
    /**
     * 根据任务ID获取流程ID
     * @param $task_id 任务ID
     * @return int
     */
    public static function getFlowIdByTask($task_id) {
        if (!is_numeric($task_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->select('flow_id')->from('core_flow_task')
                        ->where('id = :task_id', array(':task_id' => $task_id))->queryScalar();
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

    /**
     * 获取流程的上一节点
     * @param int $task_id 任务ID
     * @return int
     */
    public static function getPrevNode($task_id) {
        if (!is_numeric($task_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $current_node = self::getCurrentNode($task_id);
        if (!is_numeric($current_node)) {
            return self::GET_CURRENT_NODE_ERROR;
        }
        $flow_id = self::getFlowIdByNode($current_node);
        $sql = "SELECT id FROM core_flow_node WHERE flow_id = :flow_id AND id < :id AND deleted = 0 AND type = 0 ORDER BY id DESC LIMIT 1";
        return Yii::app()->db->createCommand($sql)->queryScalar(array(':flow_id' => $flow_id, ':id' => $current_node));
    }
    
    /**
     * 验证节点是否创建关联部门、用户、角色、岗位
     * @param int $node_id 节点ID
     * @return boolean
     */
    public static function validateNodeRelate($node_id) {
        if (!is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $relate_id = self::getFirstRelate($node_id);
        return empty($relate_id) ? false : true;
    }

    /**
     * 获取流程的当前节点的当前的关联对象
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getCurrentRelate($node_id) {
        if (is_numeric($node_id)) {
            $sql = "SELECT node_relate_id FROM core_flow_process_detail WHERE node_id = :node_id ORDER BY id DESC LIMIT 1";
            $relate_id = Yii::app()->db->createCommand($sql)->queryScalar(array(':node_id' => $node_id));
            return $relate_id;
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 获取流程节点的上一关联对象
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getPrevRelate($node_id) {
        if (!is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $current_relate = self::getCurrentRelate($node_id);
        if (empty($current_relate)) {
            return self::getFirstRelate($node_id);
        } else {
            $sql = "SELECT id FROM core_flow_node_relate WHERE node_id = :node_id AND id < :id AND deleted = 0 ORDER BY id DESC LIMIT 1";
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':node_id' => $node_id, ':id' => $current_relate));
        }
    }

    /**
     * 获取流程节点的下一关联对象
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getNextRelate($node_id) {
        if (!is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $current_relate = self::getCurrentRelate($node_id);
        if (empty($current_relate)) {
            return self::getFirstRelate($node_id);
        } else {
            $sql = "SELECT id FROM core_flow_node_relate WHERE node_id = :node_id AND id > :id AND deleted = 0 LIMIT 1";
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':node_id' => $node_id, ':id' => $current_relate));
        }
    }

    /**
     * 获取流程节点的第一个关联角色
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getFirstRelate($node_id) {
        if (is_numeric($node_id)) {
            $sql = "SELECT MIN(id) FROM core_flow_node_relate WHERE node_id = :node_id AND deleted = 0";
            return Yii::app()->db->createCommand($sql)->limit(1)->queryScalar(array(':node_id' => $node_id));
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 获取流程节点的第一个关联角色
     * @param int $node_id 节点ID
     * @return int
     */
    public static function getFirstRelateRow($node_id) {
        if (is_numeric($node_id)) {
            $sql = "SELECT MIN(id),type,relate_id FROM core_flow_node_relate WHERE node_id = {$node_id} AND deleted = 0";
            return Yii::app()->db->createCommand($sql)->queryRow();
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 获取流程节点的最后一个关联角色
     * @param int $node_id  节点ID
     * @return int
     */
    public static function getLastRelate($node_id) {
        if (is_numeric($node_id)) {
            $sql = 'SELECT MAX(id) FROM core_flow_node_relate WHERE node_id = :node_id AND deleted = 0';
            return Yii::app()->db->createCommand($sql)->queryScalar(array(':node_id' => $node_id));
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 判断关联对象ID是否是节点的最后一个关联对象
     * @param int $relate_id 节点ID
     * @return boolean
     */
    public static function isLastRelate($relate_id) {
        if (is_numeric($relate_id)) {
            $node_id = self::getNodeIdByRelate($relate_id);
            $last_relate_id = self::getLastRelate($node_id);
            return $relate_id == $last_relate_id ? true : false;
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }

    /**
     * 根据关联对象ID获取节点ID
     * @param int $relate_id 关联对象ID
     * @return int
     */
    public static function getNodeIdByRelate($relate_id) {
        if (!is_numeric($relate_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        return Yii::app()->db->createCommand()->select('node_id')->from('core_flow_node_relate')
                        ->where('deleted = 0 AND id = :id', array(':id' => $relate_id))->queryScalar();
    }

    /**
     * 初始化流程 task、process
     * @param int $flow_id 流程ID
     * @return boolean
     */
    public static function initFlowTaskByFlowId($flow_id, $sms_notice = 0) {
        //未设置审批流程
        $node_id = self::getFirstNode($flow_id);
        if (empty($node_id)) {
            return array('msg' => '请设置审批流程节点', 'flag' => false);
        }
        //验证节点对应关联对象是否有相应的人员
        $nodes = FlowNode::getNormalNodeByFlowId($flow_id);
        foreach ($nodes as $node) {
            $relates = FlowNodeRelate::getRelateByNodeId($node['id']);
            if (empty($relates)) {
                return array('msg' => $node['name'] . '审批环节无具体审批人，请联系管理员进行设置', 'flag' => false);
            } else {
                foreach ($relates as $relate) {
                    $res = self::getRelateUserList($relate['type'], $relate['relate_id'], Yii::app()->user->id);
                    if (empty($res)) {
                        return array('msg' => $node['name'] . '审批环节无具体审批人，请联系管理员进行设置', 'flag' => false);
                    }
                }
            }
        }
        //初始化task
        $date = date('Y-m-d H:i:s');
        $user_id = empty(Yii::app()->user->id) ? 1 : Yii::app()->user->id; //兼容测试用例
        $task_set = array(
            'flow_id' => $flow_id,
            'user_id' => $user_id,
            'status' => 1,
            'sms_notice' => $sms_notice,
            'created' => $date
        );
        $task_id = FlowTask::initFlowTask($task_set);
        //初始化任务节点并发送消息提醒
        FlowProcess::initFlowProcess($task_id, $node_id);
        
        //验证节点权限 如果自己可以审批自己发起的流程 则自动审批
        $res = self::verifyNodeAuthority($node_id, $task_id);
        
        //if (isset($res['prime']) && $res['prime'] === true) {
            //自动审批
            //self::approved($task_id, $node_id, $res['node_relate_id'], '通过', 2);
        //}
        //第一次初始化下一节点 消息提醒
        //self::noticeUser($node_id, $task_id, 0);
        return array('task_id'=>$task_id, 'node_id'=>$node_id, 'flag' => 0);
    }
    
    /**
     * 初始化ProcessDetail
     * @param array $param 记录审批明细
     * @return boolean
     */
    public static function insertFlowProcessDetail($param) {
        return FlowProcessDetail::insertFlowProcessDetail($param);
    }

    /**
     * 节点中角色、部门、用户、岗位审批 添加评语 修改审批状态 判断节点类型 检查是否是最后一个节点关联角色 如果是最后一个节点关联角色 更改节点状态
     * 判断是否是最后一个节点 如果是更新任务状态 如果不是最后一个节点 审批通过产生下个节点数据
     * @param int $task_id 任务ID
     * @param int $node_id 节点ID
     * @param int $node_relate_id 节点关联对象ID
     * @param string $comment 评语
     * @param int $status 审批状态
     * @return boolean
     */
    public static function approved($task_id, $node_id, $node_relate_id, $comment, $status) {
        //权限验证
        $prime_arr = self::verifyApprovedAuthority($task_id);
        if (!$prime_arr['prime']) {
            return self::NOT_APPROVED_AUTHORITY;
        }
        $date = date('Y-m-d H:i:s');
        $transaction = Yii::app()->db->beginTransaction();
        
        try {
            //判断节点ID是否是当前节点 还是允许跳过的下一节点
            $current_node = self::getCurrentNode($task_id);
            //如果不是当前节点 变更当前节点为下一节点 用来处理节点跳过直接进入下一节点审批
            if ($current_node != $node_id) {
                FlowProcess::updateFlowProcess($node_id, $task_id, $current_node);
            }
            $user_id = empty(Yii::app()->user->id) ? 1 : Yii::app()->user->id; //兼容测试用例
            $detail_set = array(
                'task_id' => $task_id,
                'approved_user_id' => $user_id,
                'node_id' => $node_id,
                'status' => $status,
                'comment' => $comment,
                'created' => $date
            );
            $node_relate_arr = explode('|', $node_relate_id);
            foreach ($node_relate_arr as $v) {
                $detail_set['node_relate_id'] = $v;
                //更新节点关联对象状态
                self::insertFlowProcessDetail($detail_set);
            }
            
            //审批状态处理
            if (2 == $status) { //状态通过
                // 审批未结束中间流程消息提醒
                // 如果是最后一个节点则不用提示
                // 直接提示审批完成的消息提醒
                if (!self::isLastNode($node_id)) {
                    self::noticeUser($node_id, $task_id, 1);
                }
                // 该节点不需要全部审批通过 或者 是否是节点下用户是否已经全部审批完成
                if (!FlowNode::isAllAllowed($node_id) || self::checkNodeIsAllApproved($node_id, $task_id)) {
                    //更新节点状态
                    FlowProcess::updateNodeStatus($task_id, $node_id, $status);
                    if (self::isLastNode($node_id)) {//是否是最后一个节点
                        //更新任务状态
                        FlowTask::updateTaskStatus($task_id, $status);
                        //审批完成消息提醒
                        self::noticeUser($node_id, $task_id, 5);
                        //审批状态修改
                        FormFlow::changeApproveStatus($task_id, ErpFlow::APPROVAL_PASS);
                    } else {
                        //初始化下一节点数据
                        $next_node = FlowNode::getNextNode($task_id, $node_id);
                        FlowProcess::initFlowProcess($task_id, $next_node);
                        //初始化下一节点 消息提醒
                        self::noticeUser($next_node, $task_id, 3);
                    }
                }
            } else { //处理未通过 和 取消状态
                //更新节点状态
                FlowProcess::updateNodeStatus($task_id, $node_id, $status);
                //更新任务状态
                FlowTask::updateTaskStatus($task_id, $status);
                if (3 == $status) { //状态不通过,撤销库存修改
                    FormFlow::cancelRevocationStock($task_id);
                }
                
                //最后一个节点不通过消息提醒
                self::noticeUser($node_id, $task_id, 4);
                //审批状态修改
                FormFlow::changeApproveStatus($task_id, ErpFlow::APPROVAL_FAIL);
            }
            
            //从IN桌面删除“等待我的事务审批”
            //TaskSummary::remove('approve', 'waitApprove', $task_id, $user_id);
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            $transaction->rollBack();
            
            return false;
        }
    }

    /**
     * 验证当前用户是否有审批权限
     * @param int $task_id 任务ID
     * @return array
     */
    public static function verifyApprovedAuthority($task_id) {
        $prime = false;
        //检查任务状态是否是流转中，如果不是返回false
        if (self::isTaskComplate($task_id)) {
            return $prime;
        }
        $current_node = self::getCurrentNode($task_id);
        if (empty($current_node)) {
            return self::NOT_DEFINE_FLOW_NODE;
        }
        //验证本节点权限
        $current_res = self::verifyNodeAuthority($current_node, $task_id);
        //验证当前用户是否拥有下一节点权限
        $is_ignored = FlowNode::isIgnored($current_node);
        if ($is_ignored) {
            $next_node = self::getNextNode($task_id, $current_node);
            if (!empty($next_node)) {
                $next_res = self::verifyNodeAuthority($next_node, $task_id);
                if (isset($next_res['prime']) && $next_res['prime'] === true) {
                    return $next_res;
                }
            }
        }
        return $current_res;
    }
    
    /**
     * 验证当前用户是否拥有改节点权限
     * @param <int> $node_id
     */
    public static function verifyNodeAuthority($node_id, $task_id) {
        $res = array();
        $relate_arr = FlowNodeRelate::getRelateByNodeId($node_id);
        if (count($relate_arr) && is_array($relate_arr)) {
            foreach ($relate_arr as $v) {
                if (self::verifyRelateAuthority($v['type'], $v['relate_id'], $task_id)) {
                    $approved_relates = FlowProcessDetail::getApprovedRelatesByTaskId($task_id);
                    if (is_array($approved_relates) && !in_array($v['id'], $approved_relates)) {
                        array_push($res, $v['id']);
                    }
                }
            }
            if (count($res)) {
                $node_relate_ids = implode('|', $res);
                return array('prime' => true, 'node_relate_id' => $node_relate_ids, 'node_id' => $node_id);
            } else {
                return false;
            }
        } else {
            return self::NOT_DEFINE_NODE_RELATE;
        }
    }

    /**
     * 验证当前用户是否拥有节点分支权限
     * @param <int> $type 关联类型
     * @param <int> $relate_id 关联对象
     */
    public static function verifyRelateAuthority($type, $relate_id, $task_id) {
        $prime = false;
        $uid = empty(Yii::app()->user->id) ? 1 : Yii::app()->user->id; //兼容测试用例
        switch ($type) {
            case 1://验证角色
                $role_arr = Account::userRoles($uid);
                $prime = in_array($relate_id, $role_arr) ? true : false;
                break;
            case 2://验证用户
                $prime = $relate_id == $uid ? true : false;
                break;
            case 3://验证部门
                $user_info = Account::user($uid);
                // 取得当前选择用户绑定的部门
                $res = FlowUserBind::bindDeptList($uid);
                $bind_list = array();
                if (count($res)) {
                    foreach ($res as $v) {
                        array_push($bind_list, $v['dept_id']);
                    }
                }
                $prime = ($relate_id == $user_info->department_id || in_array($user_info->department_id, $bind_list)) ? true : false;
                break;
            case 4://验证岗位
                break;
            case 5://部门经理验证
                $task = FlowTask::getTaskInfoById($task_id);
                $user = Account::user($task['user_id']);
                $dept = Account::department($user->department_id);
                if (isset($dept->user_id) && !empty($dept->user_id)) {
                    $prime = $uid == $dept->user_id ? true : false;
                } else {
                    $prime = false;
                }
                break;
            default:
                break;
        }
        return $prime;
    }

    /**
     * 获取当前节点关联对象的用户
     * @param <int> $type 关联类型
     * @param <int> $relate_id 关联对象
     * @param int $user_id 发起人UID
     * @return array
     */
    public static function getRelateUserList($type, $relate_id, $user_id) {
        switch ($type) {
            case 1://角色
                $res = Account::roleUsers($relate_id);
                break;
            case 2://用户
                $res = array($relate_id);
                break;
            case 3://部门
                $res = Account::departmentUsers($relate_id);
                $res = array_keys($res);
                break;
            case 4://岗位
                break;
            case 5://申请人所在部门的部门经理
                $user = Account::user($user_id);
                $dept = Account::department($user->department_id);

                if (!empty($dept->user_id)) {
                    $res = array($dept->user_id);
                } else {//TODO 如果部门经理不存在
                    $res = array();
                }
                break;
        }
        return $res;
    }

    /**
     * 获取审批节点字符串
     * @param <INT> $flow_id 流程ID
     * @return string
     */
    public static function getApproveStr($flow_id) {
        $nodes = FlowNode::getNormalNodeByFlowId($flow_id);
        $approve_name = array();
        $approve_str = array();
        if (count($nodes)) {
            foreach ($nodes as $node) {
                $relates = FlowNodeRelate::getRelateByNodeId($node['id']);
                $res = ErpFlow::getRelateAttributeList($relates);
                if (count($res) && is_array($res)) {
                    $str = implode(',', $res);
                    $approve_str[] = $str;
                }
            }
        }
        return empty($approve_str) ? '无' : implode(' → ', $approve_str);
    }

    /**
     * 获取流程适用人字符串
     * @param <INT> $flow_id 流程ID
     * @return string
     */
    public static function getStartApproveStr($flow_id) {
        $start_node = self::getStartNode($flow_id);
        $relates = self::getRelateByNodeId($start_node);
        $res = self::getRelateAttributeList($relates);
        $start_str = implode(',', $res);
        return empty($start_str) ? '无' : $start_str;
    }

    /**
     * 获取流程适用人字符串
     * @param <INT> $flow_id 流程ID
     * @return string
     */
    public static function getEndApproveStr($flow_id) {
        $end_node = self::getEndNode($flow_id);
        $relates = self::getRelateByNodeId($end_node);
        $res = self::getRelateAttributeList($relates);
        $end_str = implode(',', $res);
        return empty($end_str) ? '无' : $end_str;
    }
    
    /**
     * 获取审批记录
     * @param int $task_id 任务ID
     * @return array
     */
    public static function getApprovedRecord($task_id) {
        if (!is_numeric($task_id)) {//没有任务记录
            return self::GET_APPROVED_RECORD_ERROR;
        }
        return FlowProcessDetail::getFlowProcessDetailByTaskId($task_id);
    }
    
    /**
     * 获取所有审批表单
     * @param $group_id 表单类型ID
     * @return array
     */
    public static function listAllFlowForm($group_id = '') {
        if (empty($group_id)) {
            return Yii::app()->db->createCommand()->from('approve_form')->where('deleted = 0')->queryAll();
        } else {
            return Yii::app()->db->createCommand()->from('approve_form')->where('deleted = 0 AND group_id = :group_id', array('group_id' => $group_id))->queryAll();
        }
    }
    
    /**
     * 审批状态切换
     * @param $status 审批状态
     * @return string
     */
    public static function approveStatusConvert($status) {
        $arr = array(
            '1' => "<label style='color: gray;'>流转中</label>", 
            '2' => "<label style='color: green;'>通过</label>", 
            '3' => "<label style='color: tomato;'>不通过</label>", 
            '4' => "<label style='color: black;'>撤销</label>", 
            '5' => "<label style='color: tomato;'>作废</label>");
        return isset($arr[$status]) ? $arr[$status] : '';
    }

    /**
     * 发送IN消息提醒
     * @param $to_uids 需要发送的用户数组
     * @param $msg 消息内容
     * @param $task_id 任务ID
     */
    public static function sendInMessage($to_uids, $msg, $form_name, $task_id) {
        $link_addr = FormFlow::noticeLink($form_name, $task_id);
        //发送IN消息提醒
        if (count($to_uids) && is_array($to_uids)) {
            $flow_id = self::getFlowIdByTask($task_id);
            if ($flow_id > 0 && !empty($flow_id)) {
                $url = Yii::app()->createUrl($link_addr);
                $params = array(
                    'app' => 'approve',
                    'msg' => $msg,
                    'url' => $url,
                );
                foreach ($to_uids as $uid) {
                    if ($uid) {
                        $params['to_uid'] = $uid;
                        In::notice(array($params));
                    }
                }
            }
        }
    }

    /**
     * 消息提醒
     * @param int $node_id 节点ID
     * @param int $task_id 任务ID
     * @parma int $flag 
     * 
     *     0为审批第一个节点初始化提醒   
     *     1为审批通过提醒   
     *     2为不是最后一个节点的审批不通过提醒  没用
     *     3为审批下一个节点初始化提醒 
     *     4为最后一个节点的审批不通过提醒
     *     5为审批完成提醒
     *     6为表单修改后提醒第一审批人
     */
    public static function noticeUser($node_id, $task_id, $flag) {
        $to_uids = self::getNodeUserIdByNodeId($node_id, $task_id);
        $login = Account::user(Yii::app()->user->id);
        $data = FlowTask::getNoticeData($task_id);
        $approve_menu_list = FormFlow::getLeftMenuList();
        $approve_form_name = $approve_menu_list[empty($data['form_name']) ? "SalesOrder" : $data['form_name']];
        
        $user = Account::user($data['user_id']);
        if ($flag == 5) {
            //申请人
            $msg = "您好，{$login->name}已通过了您的{$approve_form_name}申请，此审批已完成";
            self::sendInMessage(array($user->id), $msg, $data['form_name'], $task_id);
            $end_id = FlowNode::getEndNode($data['flow_id']);
            $to_uids = self::getNodeUserIdByNodeId($end_id, $task_id);
        }
        if (!is_array($to_uids) || count($to_uids) == 0) {
            return;
        }
        
        switch($flag){
            case 0:
                $msg = "您好，{$user->name}新建了一个{$approve_form_name}，等待您的审批";
                self::sendInMessage($to_uids, $msg, $data['form_name'], $task_id);
                break;
            case 1:
                //申请人
                $msg = "您好，{$login->name}已通过了您的{$approve_form_name}";
                self::sendInMessage(array($user->id), $msg, $data['form_name'], $task_id);
                //其余审批人
                $msg = "您好，{$login->name}已通过了{$user->name}的{$approve_form_name}";
                $other_uids = array_diff($to_uids, array(Yii::app()->user->id));
                self::sendInMessage($other_uids, $msg, $data['form_name'], $task_id);
                break;
            case 3:
                $msg = "您好，{$login->name}已通过{$user->name}的{$approve_form_name}，等待您的审批";
                self::sendInMessage($to_uids, $msg, $data['form_name'], $task_id);
                break;
            case 4:
                //申请人
                $msg = "您好，{$login->name}不通过您的{$approve_form_name}";
                self::sendInMessage(array($user->id), $msg, $data['form_name'], $task_id);
                //之前审批过的人
                $to_uids = FlowProcessDetail::getTaskUserList($task_id);
                $to_uids = array_diff($to_uids, array(Yii::app()->user->id));
                $msg = "您好，{$login->name}不通过{$user->name}的{$approve_form_name}";
                self::sendInMessage($to_uids, $msg, $data['form_name'], $task_id);
                break;
            case 5:
                //审批完成节点申请
                $msg = "您好，{$user->name}发布的{$approve_form_name}已经完成";
                self::sendInMessage($to_uids, $msg, $data['form_name'], $task_id);
                break;
            case 6:
                //表单修改后提醒第一审批人
                $msg = "您好，{$user->name}修改了{$approve_form_name}";
                self::sendInMessage($to_uids, $msg, $data['form_name'], $task_id);
                break;
        }
    }
    
    public static function sendSms($to_uids, $content, $callback = null) {
        $name = array();
        foreach ($to_uids as $uid) {
            $name[] = Account::user($uid)->name;
        }
        Yii::log(implode(',', $name) . '收到的短信内容：' . $content, CLogger::LEVEL_INFO, 'service');
        return Sms::send('system', $to_uids, $content, 0, null, $callback);
    }
    
    public static function smsApprovedUri($task_id, $node_id, $uid) {
        $res = FlowNodeRelate::getRelateByNodeId($node_id);
        $node_relate_id = array();
        foreach ($res as $value) {
            $node_relate_id[] = $value['id'];
        }
        $node_relate_id = implode($node_relate_id, '|');
        if (YII_DEBUG) {
            Yii::log('网页模拟短信审批地址：' . Yii::app()->createAbsoluteUrl("approve/service/smsApproved", compact('task_id', 'node_id', 'node_relate_id', 'uid')), CLogger::LEVEL_INFO, 'service');
        }
        return "oa://" . ltrim(Yii::app()->createUrl("approve/service/smsApproved", compact('task_id', 'node_id', 'node_relate_id', 'uid')), '/');
    }

    /**
     * 获取节点下的所有用户ID
     * @param int $task_id
     * @return array
     */
    public static function getNodeUserIdByNodeId($node_id, $task_id) {
        if (is_numeric($node_id)) {
            $res = array();
            $relates = FlowNodeRelate::getRelateByNodeId($node_id);
            $task = FlowTask::getTaskInfoById($task_id);
            if (count($relates) && is_array($relates)) {
                foreach ($relates as $relate) {
                    $uids = self::getRelateUserList($relate['type'], $relate['relate_id'], $task['user_id']);
                    $res = array_merge($res, $uids);
                }
            }
            return $res;
        } else {
            return self::PARSE_PARAM_ERROR;
        }
    }
    
    /**
     * 跟进流程ID获取审批流程所有节点 包含启动审批人 和 完成审批节点
     * @param $flow_id 流程ID 
     * @return array
     */
    public static function getNodeByFlowId($flow_id) {
        return FlowNode::getNodeByFlowId($flow_id);
    }
    
    /**
     * 检查流程节点 以及 节点关联对象是否存在
     * @param int $flow_id
     * @return boolean
     */
    public static function checkNodeRelate($flow_id) {
        $nodes = self::getNodeByFlowId($flow_id);
        $flag = false;
        if (count($nodes) >= 2) {
            foreach ($nodes as $node) {
                if ($node['type'] != 2) {
                    if ($node['type'] == 0) {
                        $flag = true;
                    }
                    $relates = FlowNodeRelate::getRelateByNodeId($node['id']);
                    if (empty($relates)) {
                        return false;
                    }
                }
            }
            return $flag ? true : false;
        } else {
            return false;
        }
    }

    /**
     * 检测流程是否存在正在审批的表单
     * @param int $flow_id
     * @return boolean 
     */
    public static function isExistApproveForm($flow_id) {
        $res = FlowTask::getTaskListByFlowId($flow_id);
        if (count($res) && is_array($res)) {
            foreach ($res as $v) {
                if ($v['status'] == 1) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 复制现有流程的节点以及节点关联对象重新生成新的流程
     * @param int $flow_id 流程ID
     * @param int $node_id 节点ID
     * @param int $relate_id 节点关联对象ID
     * @param boolean $is_history 是否将原有流程置为历史
     * @return boolean
     */
    public static function copyFlow($flow_id, $node_id = '', $relate_id = '', $is_history = true) {
        $res = array();
        $flow = self::getFlowById($flow_id);
        if (is_array($flow) && count($flow)) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                //将旧的流程置为历史
                if ($is_history) {
                    self::historyFlow($flow['id']);
                }
                //复制流程
                unset($flow['id']);
                $new_flow_id = self::insertFlow($flow);
                $res['flow_id'] = $new_flow_id;
                //建立新的流程和表单的关联
                $form_name = self::getFormIdByFlowId($flow_id);
                $from_rel_id = self::insertFlowFormRel($new_flow_id, $form_name);
                if ($from_rel_id < 0) {
                    throw new CException($from_rel_id);
                }
                if ($new_flow_id) {
                    //复制节点
                    $nodes = self::getNodeByFlowId($flow_id);
                    if (count($nodes) && is_array($nodes)) {
                        foreach ($nodes as $node) {
                            $old_node_id = $node['id'];
                            unset($node['id']);
                            $node['flow_id'] = $new_flow_id;
                            $new_node_id = self::insertNode($node);
                            if ($old_node_id == $node_id) {
                                $res['node_id'] = $new_node_id;
                            }
                            //复制节点关联对象
                            $realtes = FlowNodeRelate::getRelateByNodeId($old_node_id);
                            if (count($realtes) && is_array($realtes)) {
                                foreach ($realtes as $realte) {
                                    $old_relate_id = $realte['id'];
                                    unset($realte['id']);
                                    $realte['node_id'] = $new_node_id;
                                    $new_relate_id = FlowNodeRelate::insertRelate($realte);
                                    if ($old_relate_id == $relate_id) {
                                        $res['related_id'] = $new_relate_id;
                                    }
                                }
                            }
                        }
                    }
                }
                $transaction->commit();
                return $res;
            } catch (Exception $e) {
                $transaction->rollback();
                return false;
            }
        } else {
            return self::NOT_DEFINE_FLOW;
        }
    }

    /**
     * 将流程置为历史
     * @param int $flow_id 流程ID
     * @return boolean
     */
    public static function historyFlow($flow_id) {
        return Flow::historyFlow($flow_id);
    }

    /**
     * 更新表单和流程关系
     * @param int $flow_id 流程ID
     * @param int $form_id 表单ID
     */
    public static function updateFlowFormRelByFlowId($flow_id, $form_name) {
        return Yii::app()->db->createCommand()->update('erp_form_flow', array('form_name' => $form_name), 'flow_id = :flow_id', array(':flow_id' => $flow_id));
    }

    /**
     * 根据表单ID 获取所有流程
     * @param int $form_id
     * @param int $is_history 默认0 是否显示历史流程  0为不显示
     * @return array
     */
    public static function getFlowsByFormId($form_id, $is_history = 0) {
        if (!is_numeric($form_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            if ($is_history) {
                return Yii::app()->db->createCommand()->select('cf.*')->from('approve_form_rel afr')
                                ->join('core_flow cf', 'cf.id = afr.flow_id')
                                ->where('afr.form_id = :form_id AND afr.deleted = 0', array(':form_id' => $form_id))->queryAll();
            } else {
                return Yii::app()->db->createCommand()->select('cf.*')->from('approve_form_rel afr')
                                ->join('core_flow cf', 'cf.id = afr.flow_id')
                                ->where('afr.form_id = :form_id AND afr.deleted = 0 AND cf.is_history = 0', array(':form_id' => $form_id))->queryAll();
            }
        }
    }

    /**
     * 插入节点数据
     * @param int flow_id 流程ID
     * @param array $data 节点以及节点关联对象数组
     * @param int $type 节点类型
     */
    public static function insertNodeData($flow_id, $data, $type){
        //插入节点数据
        $node_id = FlowNode::insertNode(array('flow_id' => $flow_id, 'name' => $data['node_name'], 'type' => $type, 'need_all_allowed' => $data['is_all']));
        //插入节点关联数据
        foreach ($data['data'] as $v){
            $tmp = explode('|', $v);
            $ids = explode(',', $tmp[1]);
            switch ($tmp[0]) {
                case 'user':
                    $type = 2;
                    break;
                case 'role':
                    $type = 1;
                    break;
                case 'department':
                    $type = 3;
                    break;
                case 'department_manage':
                    $type = 5;
                    break;
            }
            foreach ($ids as $id){
                $relate_id = FlowNodeRelate::insertRelate(array('node_id' => $node_id, 'type' => $type, 'relate_id' => $id));
            }
        }
    }

    /**
     * 根据流程ID 获取表单ID
     * @param int $flow_id
     * @return int
     */
    public static function getFormIdByFlowId($flow_id) {
        if (!is_numeric($flow_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->select('form_name')->from('erp_form_flow')
                            ->where('flow_id = :flow_id AND deleted = 0', array(':flow_id' => $flow_id))->queryScalar();
        }
    }

    /**
     * 根据流程ID 获取表单ID
     * @param int $flow_id
     * @return int
     */
    public static function getFormInfoByFlowId($flow_id) {
        if (!is_numeric($flow_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->select('af.*')->from('approve_form af')
                            ->join('approve_form_rel afr', 'af.id = afr.form_id')
                            ->where('afr.flow_id = :flow_id AND afr.deleted = 0', array(':flow_id' => $flow_id))->queryRow();
        }
    }

    /**
     * 根据表单ID 获取表单信息
     * @param int $flow_id
     * @return int
     */
    public static function getFormInfoById($form_id) {
        if (!is_numeric($form_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->from('approve_form af')->where('af.id = :form_id AND af.deleted = 0', array(':form_id' => $form_id))->queryRow();
        }
    }
    
    /**
     * 根据流程ID返回类型ID
     * @param int $flow_id 流程ID
     * @return $group_id
     */
    public static function getGroupIdByFlowId($flow_id) {
        return Flow::getGroupIdByFlowId($flow_id);
    }
    
    /**
     * 根据任务ID返回流程当前流转到的节点信息
     * @param int $task_id 任务ID
     * @return string 节点 名称
     */
    public static function getCurrentApproveUserByTaskId($task_id) {
        if (!is_numeric($task_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            $node = Yii::app()->db->createCommand()->select('cfn.*, cfpd.status')->from('core_flow_process_detail cfpd')
                            ->join('core_flow_node cfn', 'cfn.id = cfpd.node_id')
                            ->where('task_id = :task_id', array(':task_id' => $task_id))->order('cfpd.id desc')->limit(1)->queryRow();
            //如果没有审批节点 获取流程第一个节点
            if (empty($node)) {
                $flow_id = self::getFlowIdByTask($task_id);
                $first_node_id = self::getFirstNode($flow_id);
                $res = self::getNodeById($first_node_id);
                return $res['name'];
            } else {//如果已经产生审批节点 获取当前审批节点的审批状态
                if ($node['status'] == 3) {//不通过
                    return $node['name'];
                } else {//通过
                    //如果通过则获取节点状态
                    $status = self::getNodeStatus($task_id, $node['id']);
                    //不通过 流转中 或者是最后一个节点
                    if ($status == 3 || $status == 1 || self::isLastNode($node['id'])) {
                        return $node['name'];
                    } else {//通过 则获取下个节点名称
                        $next_node_id = self::getNextNode($task_id, $node['id']);
                        $res = self::getNodeById($next_node_id);
                        return $res['name'];
                    }
                }
            }
        }
    }

    /**
     * 根据节点状态 返回节点数据
     * @param int $status
     * @return array
     */
    public static function getNodeByProcessStatus($status) {
        if (!is_numeric($status)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            return Yii::app()->db->createCommand()->from('core_flow_process')->where('status = :status', array(':status' => $status))->queryAll();
        }
    }

    /**
     * 验证启动审批权限 只有流程第一个节点下的关联用户可以发起审批
     * @param int $flow_id 流程ID
     * @return boolean
     */
    public static function checkStartApproveAuthority($flow_id) {
        if (!is_numeric($flow_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            $node_id = self::getStartNode($flow_id);
            if (empty($node_id)) {
                return false;
            } else {
                $relates = FlowNodeRelate::getRelateByNodeId($node_id);
                if (count($relates) && is_array($relates)) {
                    foreach ($relates as $row) {
                        $res = self::getRelateUserList($row['type'], $row['relate_id'], Yii::app()->user->id);
                        if (in_array(Yii::app()->user->id, $res)) {
                            return true;
                        }
                    }
                    return false;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * 根据节点ID获取节点下所有用户
     * @param int $node_id 节点ID
     * @return array
     */
    public static function getNodeRelateAttributeByNodeId($node_id, $attribute = 'name') {
        if (!is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        } else {
            $relates = FlowNodeRelate::getRelateByNodeId($node_id);
            return self::getRelateAttributeList($relates, $attribute);
        }
    }

    /**
     * 获取当前节点关联对象的属性 默认名称
     * @param <int> $type 关联类型
     * @param <int> $relate_id 关联对象
     * @return array($attribute1,$attribute2,$attribute4,$attribute5)
     */
    public static function getRelateAttributeList($relates, $attribute = 'name') {
        $role = $user = $department = $department_manage = array();
        if (count($relates) && is_array($relates)) {
            foreach ($relates as $row) {
                $arr = self::getRelateObejct($row['type'], $row['relate_id'], $attribute);
                array_push($$arr['index'], $arr['value']);
            }
        }
        return array_merge($role, $user, $department, $department_manage);
    }

    /**
     * 当获取ID时部门经理会返回-1  之前在flow模块中使用 现在已经作废
     * 获取当前节点关联对象的名称
     * @param <int> $type 关联类型
     * @param <int> $relate_id 关联对象
     * @param <string> $attribute 需要返回的对象属性 默认名称
     * @return array('index' => 'role', 'value' => 'xxx')
     */
    public static function getRelateObejct($type, $relate_id, $attribute = 'name') {
        switch ($type) {
            case 1://获取角色属性
                $index = 'role';
                $obj = Account::role($relate_id);
                break;
            case 2://获取用户属性
                $index = 'user';
                $obj = Account::user($relate_id);
                break;
            case 3://获取部门属性
                $index = 'department';
                $obj = Account::department($relate_id);
                break;
            case 4://获取岗位属性
                break;
            case 5://部门经理 
                $index = 'department_manage';
                break;
        }
        if (isset($obj->$attribute)) {
            $value = $obj->$attribute;
        } else {
            $value = 'name' == $attribute ? '申请人所在部门的部门经理' : -1;
        }
        return array('index' => $index, 'value' => $value);
    }

    /**
     * flow模块使用 已经作废
     * 根据节点ID获取用户ID、角色ID、部门ID数组
     * @param <array> $node_id 节点ID
     * @return array
     */
    public static function getAllIdsListByNodeId($node_id) {
        if (!is_numeric($node_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $role = $user = $department = $department_manage = array();
        $relates = FlowNodeRelate::getRelateByNodeId($node_id);
        if (count($relates)) {
            foreach ($relates as $v) {
                $arr = self::getRelateObejct($v['type'], $v['relate_id'], 'id');
                array_push($$arr['index'], $arr['value']);
            }
        }
        return array('role' => $role, 'user' => $user, 'department' => $department, 'department_manage' => $department_manage);
    }

    /**
     * 删除流程下的所有数据 包含节点和节点关联对象
     * @param int $flow_id
     */
    public static function delFlowData($flow_id) {
        if (!is_numeric($flow_id)) {
            return self::PARSE_PARAM_ERROR;
        }
        $nodes = FlowNode::getNodeByFlowId($flow_id);
        if (is_array($nodes) && count($nodes)) {
            foreach ($nodes as $node) {
                //删除节点关联对象
                FlowNodeRelate::delRelateByNodeId($node['id']);
                //删除节点
                FlowNode::delNodeById($node['id']);
            }
        }
    }

    /**
     * 获取当组织架构树
     * @param <array> $user_ids 已选用户数组
     */
    public static function userTree($user_ids = array()) {
        $id = 'user';
        $db = Yii::app()->db;
        // 所有用户
        $users = Account::users();
        // 所有部门
        $depts = Account::departments();

        foreach ($depts as $dept) {
            $dept = (object) $dept;
            $data[] = array(
                'text' => $dept->name,
                'htmlOptions' => array('data-id' => $dept->id, 'data-type' => 'dept', 'id' => 'dept_' . $dept->id),
                'id' => 'dept_' . $dept->id,
           		'expanded' => false,
                'parent_id' => 'dept_' . $dept->parent_id,
            );
        }
        foreach ($users as $user) {
            $user = (object) $user;
            $data[] = array(
                'text' => $user->name,
                'htmlOptions' => array('data-id' => $user->id, 'data-type' => 'user', 'id' => 'user_' . $user->id),
                'id' => 'user_' . $user->id,
                'parent_id' => 'dept_' . $user->department_id,
            );
        }

        $tree = array('tree' => Tree::treeView($data, 'dept_0'), 'id' => $id, "left_name" => "组织架构", "right_name" => "已选人员", "ids" => json_encode($user_ids));
        return Yii::app()->controller->renderPartial('_tree', $tree, true, false);
    }

    /**
     * 获取角色树
     * @param <array> $role_ids 已选角色数组
     */
    public static function roleTree($role_ids = array()) {
        $id = 'role';
        $db = Yii::app()->db;
        // 所有角色
        $roles = Account::roles();
        $data = array();
        foreach ($roles as $role) {
            $role = (object) $role;
            $data[] = array(
                'text' => $role->name,
                'htmlOptions' => array('data-id' => $role->id, 'data-type' => 'user', 'id' => 'role_' . $role->id),
                'id' => 'role_' . $role->id,
                'parent_id' => 'role_0',
            );
        }
        $tree = array('tree' => Tree::treeView($data, 'role_0'), 'id' => $id, "left_name" => "角色列表", "right_name" => "已选角色", "ids" => json_encode($role_ids));
        return Yii::app()->controller->renderPartial('_tree', $tree, true, false);
    }

    /**
     * 获取部门树
     * @param <array> $department_ids 已选部门数组
     */
    public static function departmentTree($department_ids = array()) {
        $id = 'department';
        $db = Yii::app()->db;
        // 所有角色
        $departments = Account::departments();
        $data = array();
        foreach ($departments as $department) {
            $department = (object) $department;
            $data[] = array(
                'text' => $department->name,
                'htmlOptions' => array('data-id' => $department->id, 'data-type' => 'user', 'id' => 'department_' . $department->id),
                'id' => 'department_' . $department->id,
                'parent_id' => 'department_0',
            );
        }
        $tree = array('tree' => Tree::treeView($data, 'department_0'), 'id' => $id, "left_name" => "部门列表", "right_name" => "已选部门", "ids" => json_encode($department_ids));
        return Yii::app()->controller->renderPartial('_tree', $tree, true, false);
    }

    /**
     * 获取表单的短信内容
     * @param string $formula 短信计算公式
     * @param string $json_data 短信的计算数据
     * @return string 短信内容
     */
    public static function renderSmsContent($formula, $json_data) {
         extract(CJSON::decode($json_data)); // 将json数据转为变量
         return eval("$formula");
    }
}
