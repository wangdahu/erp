<?php
/**
 * 进销存审批
 * @author ben
 *
 */
class ApproveController extends PssController{
	
    /**
     * 审批流程
     * @param int $type
     */
    public function actionIndex($form_name='SalesOrder'){
        $this->breadcrumbs['设置'] = array('/pss/setting');
        $this->breadcrumbs[] = '审批设置';
/*        if (!class_exists($form_name)){
            throw new CHttpException(404, '请求的页面不存在。');
        }*/
        $model = new $form_name;
        $this->render('index', array('model' => $model, 'form_name' => $form_name));
    }
    
    /**
     * 审批事务流程添加
     * @param int $group_id 表单类型ID
     * @param int $form_id 表单ID
     */
    public function actionCreateFlow($form_name) {
        $this->breadcrumbs['设置'] = array('/pss/setting');
        $this->breadcrumbs['审批设置'] = array('/pss/approveflow/index&type=1');
        $this->breadcrumbs[] = '新增流程';
        $flows = PssFlow::listAllFlowExceptHistory(0);//进销存已有审批流程       参数：1为oa正常审批流程，0为进销存审批流程
        $this->render('createflow', array('flows' => $flows, 'is_edit' => 0,'form_name' => $form_name));
    }
    
    /**
     * 审批事务流程添加
     * @param int $group_id 表单类型ID
     * @param int $form_id 表单ID
     * @param int $flow_id 流程ID
     */
    public function actionUpdateFlow($form_name, $flow_id) {
        $flows = PssFlow::listAllFlowExceptHistory(0);
        $this->render('createflow', array('flows' => $flows, 'is_edit' => 1,'form_name' => $form_name, 'flow_id' => $flow_id));
    }
    
    /**
     * 审批流程插入
     * @return booleanhasRoleWhichFlowEveryNode
     */
    public function actionInsertFlow(){
        $start_node_data = $_POST['start_node'];
        $end_node_data = $_POST['end_node'];
        $approve_node_data = $_POST['approve_node'];
        $transaction = Yii::app()->db->beginTransaction();
        try {
            //流程编辑
            if(isset($_POST['flow_id']) && $_POST['flow_id'] > 0){
                //判断是否存在正在审批的表单
                $flow_id = $_POST['flow_id'];
                $flag = PssFlow::isExistTask($flow_id);
                if(-1004 === $flag){
                    throw new CException('流程不存在');
                }else if(true === $flag){//任务已存在
                    //如果该流程已经存在启动审批的表单 则把原有的流程置为历史
                    PssFlow::historyFlow($flow_id);
                    //新建流程
                    $flow_id = PssFlow::insertFlow(array('group_id' => $_POST['group_id'], 'name' => $_POST['flow_name']));
                    //建立表单关联
                    PssFlow::insertFlowFormRel($flow_id, $_POST['form_name']);
                }else{//任务不存在
                    //直接更新流程名称
                    PssFlow::updateFlowById(array('name' => $_POST['flow_name']), $flow_id);
                    //删除流程节点以及节点关联对象数据
                    PssFlow::delFlowData($flow_id);
                }
            }else{
                //新建流程
                $flow_id = PssFlow::insertFlow(array('group_id' => $_POST['group_id'], 'name' => $_POST['flow_name']));
                //建立表单关联
                PssFlow::insertFlowFormRel($flow_id, $_POST['form_name']);
            }
            //开始节点数据插入
            PssFlow::insertNodeData($flow_id, $start_node_data, 1);
            //审批节点数据出入
            foreach ($approve_node_data as $data) {
                PssFlow::insertNodeData($flow_id, $data, 0);
            }
            //结束节点数据插入
            if(!empty($end_node_data)){
                 PssFlow::insertNodeData($flow_id, $end_node_data, 2);
            }
            $transaction->commit();
            echo 1;
        } catch (Exception $e) {
            $transaction->rollback();
            echo 0;
        }
        Yii::app()->end();
    }
    
    /**
     * 复制流程
     * @param int $flow_id  流程ID
     * @param int $form_id 表单ID
     * @param int $form_group_id 表单类型ID
     * @return boolean
     */
    public function actionCopyFlow($flow_id, $form_name, $group_id){
        if(isset($_POST['new_flow_name']) && !empty($_POST['new_flow_name'])){
            $group_id = PssFlow::getGroupIdByFlowId($flow_id);
            if($group_id == '0'){
                $res = PssFlow::copyFlow($flow_id, '', '' , false);
                
                if(is_array($res) && count($res)){
                    PssFlow::updateFlowById(array('name' => $_POST['new_flow_name']), $res['flow_id']);
                    //更新流程表单对应关系
                    PssFlow::updateFlowFormRelByFlowId($res['flow_id'], $_POST['current_form_name']);
                    $this->redirect(array('/pss/approve/index', 'form_name' => $form_name));
                }else{
                    throw new CException('操作失败');
                }
            }else{
                throw new CException('错误的参数');
            }
        }else{
            $flow = PssFlow::getFlowById($flow_id);
            if(is_array($flow) && count($flow)){
                $allforms = PssFlow::listAllFlowExceptHistory();
                $this->renderPartial('_copy_flow', array('flow' => $flow, 'allforms' => $allforms, 'form_name' => $form_name), false, true);
            }else{
                throw new CException('错误的参数');
            }
        }
    }
    
    /**
     * 删除流程
     * @param int $group_id 表单类型ID
     * @param int $flow_id 流程ID
     * @param int $form_id 表单ID
     */
    public function actionDeleteFlow($group_id, $flow_id, $form_name) {
        //判断是否存在正在审批的表单
        $flag = PssFlow::historyFlow($flow_id);
        if(-1004 === $flag){
            throw new CException('流程不存在');
        }else{
            echo json_encode(array('status' => 1, 'location' => $this->createUrl('/pss/approve/index',array('form_name' => $form_name))));
        }
        Yii::app()->end();
    }
    
    /**
     * 根据流程ID获取流程图
     * @param int $flow_id
     * @return string
     */
    public function actionGetFlowHtml($flow_id){
        $is_edit = $flow_id > 0 ? 1 : 0 ;
        if($is_edit){
            $nodes = PssFlow::getNodeByFlowId($flow_id);
            $this->renderPartial('_flow_data', array('is_edit' => $is_edit, 'nodes' => $nodes));
        }else{
            $this->renderPartial('_flow_data', array('is_edit' => $is_edit));
        }
    }
    
    /**
     * 新增流程节点
     */
    public function actionCreateNode($user, $role, $department, $div_id, $modify, $is_all,$department_manage) {
        $res = PssFlow::getSelectedList($user, $role, $department, $department_manage);
        $this->renderPartial('_add_node_form', array('is_all' => $is_all, 'is_create' => $res['is_create'],'div_id' => $div_id,'modify' => $modify,
                                'user_list' => $res['user_list'], 'role_list' => $res['role_list'], 'department_list' => $res['department_list'],  'department_manage_list' => $res['department_manage_list']), false, true);
    }
    
    /**
     * 流程结束设置通知人员节点
     */
    public function actionCreateEndNode($user,$role,$department,$sms_notice) {
        $res = PssFlow::getSelectedList($user, $role, $department);
        $this->renderPartial('_add_end_node_form', array('is_create' => $res['is_create'], 'user_list' => $res['user_list'], 'sms_notice' => $sms_notice,
                                'role_list' => $res['role_list'], 'department_list' => $res['department_list']), false, true);
    }
    
    /**
     * 流程发起人节点
     */
    public function actionCreateStartNode($user,$role,$department) {
        $res = PssFlow::getSelectedList($user, $role, $department);
        $this->renderPartial('_add_start_node_form', array('is_create' => $res['is_create'], 'user_list' => $res['user_list'],
                                 'role_list' => $res['role_list'], 'department_list' => $res['department_list']), false, true);
    }
    
    /**
     * 渲染启动审批流程表单页面
     * @param int form_id int 表单ID
     */
    public function actionForm($form_id) {
        $userinfo = Account::user(Yii::app()->user->id);
        $deptinfo = Account::department($userinfo->department_id);
        $db = Yii::app()->db;
        if (!is_numeric($form_id)) {
            throw new CException('错误的参数');
        }
        
        //处理表单对应的流程
        $flows = PssFlow::getFlowsByFormId($form_id);
        $list = array();
        if(is_array($flows) && count($flows)){
            foreach ($flows as $k => $flow) {
                //验证权限
                $auth = PssFlow::checkStartApproveAuthority($flow['id']);
                //检查流程节点 和 节点关联对象
                $flag = PssFlow::checkNodeRelate($flow['id']);
                if($auth === true && $flag === true){
                    $approve_str = PssFlow::getApproveStr($flow['id']);
                    $list[$k]['auth'] = $auth;
                    $list[$k]['flag'] = $flag;
                    $list[$k]['approve_str'] = $approve_str;
                    $list[$k]['flow'] = $flow;
                }
            }
        }
        $this->render('form', array('list' => $list,'username' => $userinfo->name,
                                    'position' => $userinfo->position, 'department' => $deptinfo->name));
    }
    
    /**
     * 表单审批
     * @param <int> $task_id 任务ID
     * @param <int> $node_id 节点ID
     * @param <int> $node_relate_id 关联对象ID
     * @param <int> $status 审批状态
     * @param <string> $comment 评语
     * @return <boolean>
     */
    public function actionApproved($task_id, $node_id, $node_relate_id, $status, $comment) {
        if (Yii::app()->request->isAjaxRequest) {
            if (!is_numeric($task_id) || !is_numeric($node_id) || empty($node_relate_id) || !is_numeric($status)) {
                echo json_encode(array('msg' => '参数错误', 'flag' => false));
            } else {
                // 审批之前判断事务是否已经完成
                if(PssFlow::isTaskComplate($task_id)) {
                    echo json_encode(array('msg' => '当前事务已审批，您此次审批操作无效。', 'flag' => false));
                    Yii::app()->user->setFlash("page_flash", json_encode(array('msg' => '当前事务已审批，您此次审批操作无效。', 'flag' => false, "type" => 'error')));
                    Yii::app()->end();
                } else {
                    // 判断事务是否在本节点完成审批
                    if (1 != PssFlow::getNodeStatus($task_id, $node_id)) {
                        echo json_encode(array('msg' => '当前事务已审批，您此次审批操作无效。', 'flag' => false));
                        Yii::app()->user->setFlash("page_flash", json_encode(array('msg' => '当前事务已审批，您此次审批操作无效。', 'flag' => false, "type" => 'error')));
                        Yii::app()->end();
                    }
                    
                    if (true == PssFlow::approved($task_id, $node_id, $node_relate_id, $comment, $status)) {
                        echo json_encode(array('msg' => '当前事务审批已完成。', 'flag' => true));
                        Yii::app()->user->setFlash("page_flash", json_encode(array('msg' => '当前事务审批已完成。', 'flag' => true, "type" => 'ok')));
                        Yii::app()->end();
                    } else {
                        echo json_encode(array('msg' => '操作失败', 'flag' => false));
                        Yii::app()->user->setFlash("page_flash", json_encode(array('msg' => '操作失败', 'flag' => false, "type" => 'error')));
                    }
                }
            }
        } else {
            throw new CHttpException(400, '无效的请求，请不要再次发送此请求。');
        }
    }
    
    /**
     * 判断审批环节具体审批人，
     * 是否有具体审批人，
     * 具体审批人是否填写手机号码。
     *
     * @param $flow_id
     * @param $sms_flag 默认0 当为1时判断具体审批人是否有填写手机号码
     **/
    public function actionHasRoleWhichFlowEveryNode($flow_id) {
        $_user_id = Yii::app()->user->id;
        $_nodes = PssFlow::getNormalNodeByFlowId($flow_id);
        foreach ($_nodes as $_node) {
            $_relates = PssFlow::getRelateByNodeId($_node['id']);
            if (empty($_relates)) {
                $_messages = array("msg" => $_node['name'].'审批环节无具体审批人，此流程不可用，请联系管理员进行设置', 'flag' => false);
                echo json_encode($_messages);
                Yii::app()->end();
            } else {
                foreach ($_relates as $_relate) {
                    $_roles = PssFlow::getRelateUserList($_relate['type'], $_relate['relate_id'], $_user_id);
                    if (empty($_roles)) {
                        $_messages = array("msg" => $_node['name'].'审批环节无具体审批人，此流程不可用，请联系管理员进行设置', 'flag' => false);
                        echo json_encode($_messages);
                        Yii::app()->end();
                    }
                }
            }
        }
        $_messages = array("msg" => false, 'flag' => true);
        echo json_encode($_messages);
        Yii::app()->end();
    }
    
    public function actionRepealFlowForm($id, $flow_id, $task_id, $status, $absolute_path, $class_name) {
        $url = array("/" . $absolute_path . "/view","id" => $id);
        
        try {
            if (empty($status)) {
                throw new CException("未知的操作！");
            }
            
            $_user_id = Yii::app()->user->id;
            $_form = Yii::app()->db->createCommand()->from("core_flow_task")->where("id = :task_id",array(":task_id" => $task_id))->queryRow();
            if (in_array($_form["status"],array(4,5))) {
                throw new CException("该申请已经撤销或作废！");
            }
            
            $task = new CoreFlowProcessDetail;
            $this->performAjaxValidation($task);
            if (Yii::app()->request->isPostRequest) {
                $_request_param = Yii::app()->request->getPost("CoreFlowProcessDetail");
                
                // 撤销
                if ($_request_param && "4" == $status) {
                    // 只有本人才可以申请撤销
                    if ($_user_id != $_form['user_id']) {
                        throw new CException("只有发布人才可以撤销！");
                    }
                    // 未完成审批的事务才可以撤销
                    if ('1' != $_form['status']) {
                        throw new CException("该申请已经通过审核，不能撤销！");
                    }
                    
                    // 已审和流转中
                    $_to_users = array_merge($this->_getApproveUsers($task_id, 4), $this->_getApproveUsers($task_id, 1));
                    //$_form_info = PssFlow::getFormInfoByFlowId($flow_id);
                    $_form_info = FormFlow::getLeftMenuList();
                    $_user_info = Account::user(Yii::app()->user->id);
                    $_msg = "您好！{$_user_info->name} 撤销了 {$_form_info[$class_name]}!";
                    
                    try {
                        // 在当前流转人的IN桌面取消本数据
                        $curUsers = $this->_getApproveUsers($task_id, 1);
                        foreach ($curUsers as $uid) {
                            TaskSummary::remove('approve', 'waitApprove', $task_id, $uid);
                        }
                        
                        $_node_data = Yii::app()->db->createCommand()
                            ->select("a.id AS node_id, b.id AS node_relate_id")
                            ->from("core_flow_node a")->join("core_flow_node_relate b","a.id = b.node_id")
                            ->where("a.flow_id = :flow_id AND a.type = :type", array(":flow_id" => $_form['flow_id'], ":type" => 1))
                            ->queryRow();
                        
                        $_comment = $_request_param['comment'];
                        $_sms_notice = $_request_param['sms_notice'];
                        
                        $_transaction = Yii::app()->db->beginTransaction();
                        // core_flow_task 更新task状态为撤销
                        CoreFlowTask::model()->updateByPk($task_id,array("status" => $status));
                        // core_flow_process
                        Yii::app()->db->createCommand()->delete("core_flow_process", "status = 1 AND task_id = :task_id", array(":task_id" => $task_id));
                        // core_flow_process_detail 增加一条撤销人和撤销原因的纪录
                        Yii::app()->db->createCommand()->insert("core_flow_process_detail", array("task_id" => $task_id, "node_id" => $_node_data['node_id'], "node_relate_id" => $_node_data['node_relate_id'], "approved_user_id" => $_form['user_id'], "status" => $status, "comment" => $_comment, "created" => date('Y-m-d H:i:s')));
                        // approve_form_record 纪录撤销原因
                        Yii::app()->db->createCommand()->update("approve_form_record", array("comment" => $_comment, "updated" => date("Y-m-d H:i:s")), "task_id = :task_id", array(":task_id" => $task_id));
                        $_transaction->commit();
                        
                        // 发送消息
                        PssFlow::sendInMessage($_to_users, $_msg, $class_name, $task_id);
                        if ($_sms_notice) {
                            PssFlow::sendSms($_to_users, $_msg);
                        }
                        
                        FormFlow::changeApproveStatus($task_id, 4);//审批状态修改
                        
                        // 重整页面
                        Yii::app()->user->setFlash("page_flash", json_encode(array("msg" => "您已成功撤销申请！", "type" => "ok")));
                        $this->redirect($url);
                    } catch (Exception $e) {
                        $_transaction->rollback();
                        Yii::app()->user->setFlash("page_flash", json_encode(array("msg" => $e->getMessage(), "type" => "error")));
                        $this->redirect($url);
                    }
                }
                
//                // 作废
//                if ($_request_param && "5" == $status) {
//                    // 只有完成审批的事务才可以作废
//                    if (!in_array($_form['status'], array(2,3))) {
//                        throw new CException("该事务正在流转或者已经被撤销！");
//                    }
//                    // 只有审批的最后一个环节的审批人才可以作废
//                    // 找到最后一个审批环节
//                    $_last_node_id = Yii::app()->db->createCommand()->select("id")->from("core_flow_node")->where("type = 0 AND deleted = 0 AND flow_id = :flow_id", array(":flow_id" => $_form['flow_id']))->order("id DESC")->limit(1)->queryScalar();
//                    $_node_relate_id = Yii::app()->db->createCommand()->select("id")->from("core_flow_node_relate")->where("deleted = 0 AND node_id = :node_id", array(":node_id" => $_last_node_id))->queryScalar();
//                    // 找到这个环节下所有的审批人
//                    $_last_approve_users = PssFlow::getNodeUserIdByNodeId($_last_node_id, $task_id);
//                    // 看当前用户是否属于这个环节
//                    if (!in_array($_user_id, $_last_approve_users)) {
//                        throw new CException("您不是最后审批人，所以不能作废该事务！");
//                    }
//                    $_comment = $_request_param['comment'];
//                    $_sms_notice = $_request_param['sms_notice'];
//                    
//                    // 最后审批人
//                    $_action_user = Yii::app()->db->createCommand()->select("approved_user_id")->from("core_flow_process_detail")->where("task_id = :task_id AND status IN (2,3)", array(":task_id" => $task_id))->order("id DESC")->limit(1)->queryScalar();
//                    // 有作过审批操作的人
//                    $_action_users = Yii::app()->db->createCommand()->select("approved_user_id")->from("core_flow_process_detail")->where("task_id = :task_id AND status IN (2,3)", array(":task_id" => $task_id))->queryAll();
//                    foreach ($_action_users as $_users) {
//                        $_user[] = $_users['approved_user_id'];
//                    }
//                    // 如果是自己审批完成则不用通知
//                    $_last_approve_users = $this->_getApproveUsers($task_id, 3);
//                    if ($_action_user == Yii::app()->user->id) {
//                        $_last_approve_users = array_diff($_last_approve_users, array($_action_user));
//                    }
//                    /* 发起人信息 */
//                    $_approve_user_info = Account::user($_form['user_id']);
//                    $_approve_to_user = array($_form['user_id']);
//                    
//                    // 最后审批环节，之前做过审批操作
//                    $_to_users = array_merge($_last_approve_users, $_user);
//                    $_form_info = PssFlow::getFormInfoByFlowId($flow_id);
//                    $_user_info = Account::user(Yii::app()->user->id);
//                    $_msg = "您好！{$_user_info->name} 作废了{$_approve_user_info->name}的 {$_form_info['name']}!";
//                    $_approve_receive_msg = "您好！{$_user_info->name} 作废了您的 {$_form_info['name']}!";
//                    
//                    try {
//                        $_transaction = Yii::app()->db->beginTransaction();
//                        // core_flow_task 更新task状态为作废
//                        
//                        CoreFlowTask::model()->updateByPk($task_id,array("status" => $status));
//                        // core_flow_process_detail 增加一条作废原因
//                        Yii::app()->db->createCommand()->insert("core_flow_process_detail", array("task_id" => $task_id, "node_id" => $_last_node_id, "node_relate_id" => $_node_relate_id, "approved_user_id" => $_user_id, "status" => $status, "comment" => $_comment, "created" => date("Y-m-d H:i:s")));
//                        // approve_form_record 纪录作废原因
//                        Yii::app()->db->createCommand()->update("approve_form_record", array("comment" => $_comment, "updated" => date("Y-m-d H:i:s")), "task_id = :task_id", array(":task_id" => $task_id));
//                        $_transaction->commit();
//                        
//                        // 发送消息
//                        PssFlow::sendInMessage($_to_users, $_msg, $task_id);
//                        PssFlow::sendInMessage($_approve_to_user, $_approve_receive_msg, $task_id);
//                        if ($_sms_notice) {
//                            PssFlow::sendSms($_to_users, $_msg);
//                        }
//                        // 重整页面
//                        $model = ApproveFormRecord::model()->find("task_id=$task_id");
//                        $url = array("/approve/task/view","id" => $model->id);
//                        Yii::app()->user->setFlash("page_flash",json_encode(array("msg" => "您已成功作废申请！", "type" => "ok")));
//                        $this->redirect($url);
//                    } catch (Exception $e) {
//                        $_transaction->rollback();
//                        throw new Exception($e->getMessage());
//                    }
//                }
            }
            $renderData = array(
                "task" => $task,
                "flow_id" => $flow_id,
                "task_id" => $task_id,
                "status" => $status,
            );
            Yii::app()->clientScript->scriptMap['jquery.js'] = false;
            $this->renderPartial("_repeal_flow", $renderData, false, true);
        } catch (Exception $e) {
            header("Status: 501 Not Found!");
            Yii::app()->user->setFlash("page_flash",json_encode(array("msg" => $e->getMessage(), "type" => "error")));
            Yii::app()->end();
        }
    }
    
    protected function _getApproveUsers($task_id, $need_tag = '0') {
        $_flow_id = PssFlow::getFlowIdByTask($task_id);
        switch ($need_tag) {
        case 1:
            // 当前审批环节的审批人(正在流转)
            $_node_id = PssFlow::getCurrentNode($task_id);
            break;
        case 2:
            // 第一批审批人
            $_node_id = PssFlow::getFirstNode($_flow_id);
            break;
        case 3:
            // 最后一批审批人
            $_node_id = Yii::app()->db->createCommand()->select("id")->from("core_flow_node")->where("type = 0 AND deleted = 0 AND flow_id = :flow_id", array(":flow_id" => $_flow_id))->order("id DESC")->limit(1)->queryScalar();
            break;
        case 4:
            // 已经审批
            $_node_id = Yii::app()->db->createCommand()->select("node_id")->from("core_flow_process_detail")->where("task_id = :task_id",array(":task_id" => $task_id))->queryAll();
            break;
        default:
            $_node_id = PssFlow::getCurrentNode($task_id);
            break;
        }

        // 找到这个环节下所有的审批人
        if (!is_array($_node_id)) {
            $_approve_users = PssFlow::getNodeUserIdByNodeId($_node_id, $task_id);
        } else {
            $_approve_users = array();
            foreach ($_node_id AS $_node) {
                $_users = PssFlow::getNodeUserIdByNodeId($_node['node_id'], $task_id);
                $_approve_users = array_merge($_approve_users, $_users);
            }
        }
        return array_unique($_approve_users);
    }
}