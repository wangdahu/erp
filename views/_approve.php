    <?php $task_id = empty($model->approval_id) ? '0' : $model->approval_id;?>
    
    <div class="clearfix" style='margin-top: 10px;'>
        <div class="cell span20">
            <label for="approve_flow" class="required">审批流程</label>
            <div class="item">
                <div class="main">
                    <?php 
                    $flow_info = FlowTask::getTaskInfoById($task_id);
                    echo PssFlow::getApproveStr(empty($flow_info['flow_id']) ? '0' : $flow_info['flow_id']);?>
                </div>
            </div>
        </div>
    </div>
    
    <div style='margin: 10px 0 0 30px; font-weight: bold; font-size: 18px;'>
    	<label>审批意见</label>
    </div>
    <div style='margin: 1px 0 0 10px; border-style: none; border-top: solid 2px #CCC;; width: 98%;'></div>
    
    <div>
        <?php $approve_record = PssFlow::getApprovedRecord($task_id);
        if (count($approve_record)) { ?>
            <?php foreach ($approve_record as $k => $val) { ?>
            <?php $_user = Account::user($val['approved_user_id']); ?>
            <?php $_deptname = Account::department($_user->department_id); ?>
            <div class="approved_result_box" style='margin-left: 30px;'>
                <div style='margin: 15px 0 5px 0;'>
                    <label><?php echo $_deptname->name; ?></label>
                    <label><?php echo $_user->name; ?></label>
                    <label><?php echo substr($val['created'], 0, 16); ?></label>
                    <?php if (2 == $val['status']) { ?>
                    <label><strong class="green"><?php echo PssFlow::approveStatusConvert($val['status']); ?></strong></label>
                    <?php } else { ?>
                    <label><strong class="red"><?php echo PssFlow::approveStatusConvert($val['status']); ?></strong></label>
                    <?php } ?>
                    <label>审批</label>
                </div>
                <div style='margin-bottom: 20px;'>
                    <label>
                         <?php if (4 == $val['status']) echo '<span>撤销事由：</span>'; ?>
                         <?php if (5 == $val['status']) echo '<span>作废事由：</span>'; ?>
                         <?php if (in_array($val['status'], array(2,3))) echo '<span>审批意见：</span>'; ?>
                        <span><?php echo $val['comment']; ?></span>
                    </label>
                </div>
            </div>
            <?php } ?>
        <?php }else{?>
            <div class="approved_result_box" style='margin-left: 30px;'>
                <div style='margin: 15px 0 5px 0;'>
                    <label>
                        <span>无审批意见</span>
                    </label>
                </div>
            </div>
        <?php } ?>
        <?php //权限验证
        $authority = PssFlow::verifyApprovedAuthority($task_id);
        if (isset($authority['prime']) && $authority['prime'] === true) { ?>
        <div style='color: #145A83; margin: 10px 0 0 30px;' id="approval">
            <ul>
                <li>请输入审批意见：</li>
                <li style="height:65px;"><textarea id="comment" name="comment" cols="60" rows="4"></textarea></li><br>
                <li>
                    <button type="button" data-status="2" class="button highlight" ><span class="ok" data-status="2">通过</span></button>
                    <button type="button" data-status="3"><span class="cancel" data-status="3">不通过</span></button>
                    <input type="hidden" value='<?php echo $authority['node_relate_id']; ?>' id="node_relate_id" name="node_relate_id" />
                    <input type="hidden" value='<?php echo $authority['node_id']; ?>' id="node_id" name="node_id" />
                    <input type="hidden" value='<?php echo $task_id; ?>' id="task_id" name="task_id" />
                </li>
            </ul>
        </div>
        <?php } ?>
    </div>
    
<script>
    jQuery(function($) {
        $("#approval button").bind('click', function(){
            $(this).prop('disabled',true);
            var status = $(this).data('status');
            var task_id = $("#task_id").val();
            var node_relate_id = $("#node_relate_id").val();
            var node_id = $("#node_id").val();
            var comment = $("#comment").val();
            var params = {status:status, task_id:task_id, node_relate_id:node_relate_id, node_id:node_id, comment:comment};
            $.getJSON("/index.php?r=pss/approve/approved&" + $.param(params), function(data){
                var opt = {
                    message: data.msg,
                    _reload: function(){
                        location.reload();
                    }
                };
                if(data.flag == true){
                    opt._reload();
                }else{
                    opt._reload();
                }
            });
        });
    });
</script>