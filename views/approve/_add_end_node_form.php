<?php
$form = $this->beginWidget('ActiveForm', array('id' => 'node_form','enableAjaxValidation'=>true));
?>

<div style="padding-left:40px; margin-bottom:10px; margin-top:10px;">
         审批完成后通知
</div>

<div class="clearfix">
    <div class="cell">
        <div class="item span13">
            <div class="main">
                <span style="padding-left:40px;" id="notice_info">
                    <?php
                        if($is_create){
                    ?>
                            <input id="notice_info_role" value="role" type="checkbox" name="notice_info_role" />
                            <label for="notice_info_role">指定角色</label>

                            <input id="notice_info_department" value="department" type="checkbox" name="notice_info_department" />
                            <label for="notice_info_department">指定部门</label>

                            <input id="notice_info_user" value="user" type="checkbox" name="notice_info_user" />
                            <label for="notice_info_user">指定人员</label>
                    <?php
                    }else{
                        $user_checked = (is_array($user_list) && count($user_list)) ? 'checked' : '';
                        $role_checked = (is_array($role_list) && count($role_list)) ? 'checked' : '';
                        $department_checked = (is_array($department_list) && count($department_list)) ? 'checked' : '';
                    ?>
                            <input id="notice_info_role" value="role" type="checkbox" name="notice_info_role" <?php echo $role_checked; ?> />
                            <label for="notice_info_role">指定角色</label>
                            <input id="notice_info_department" value="department" type="checkbox" name="notice_info_department" <?php echo $department_checked; ?> />
                            <label for="notice_info_department">指定部门</label>
                            <input id="notice_info_user" value="user" type="checkbox" name="notice_info_user" <?php echo $user_checked; ?> />
                            <label for="notice_info_user">指定人员</label>
                    <?php
                        }
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <div class="item">
            <div class="main" style="padding-left:40px;overflow-y:auto;height: 300px; width:450px">
                <?php
                    if($is_create){
                ?>
                    <div id="role" style="display:none;">
                        <?php $this->widget('ext.Picker', array('id' => 'picker_role', 'type' => 'role', 'picked' => array())); ?>
                    </div>

                    <div id="user" style="display:none;">
                        <?php $this->widget('ext.Picker', array('id' => 'picker_user', 'type' => 'user', 'picked' => array())); ?>
                    </div>

                    <div id="department" style="display:none;">
                        <?php $this->widget('ext.Picker', array('id' => 'picker_department', 'type' => 'depart', 'picked' => array())); ?>
                    </div>
                <?php
                    }else{
                        $department_style = (is_array($department_list) && count($department_list)) ? '' : 'display:none';
                        $user_style = (is_array($user_list) && count($user_list)) ? '' : 'display:none';
                        $role_style = (is_array($role_list) && count($role_list)) ? '' : 'display:none';
                ?>
                        <div id="user" style="<?php echo $user_style; ?>">
                             <?php $this->widget('ext.Picker', array('id' => 'picker_user', 'type' => 'user', 'picked' => $user_list)); ?>
                        </div>
                        <div id="role" style="<?php echo $role_style; ?>">
                             <?php $this->widget('ext.Picker', array('id' => 'picker_role', 'type' => 'role', 'picked' => $role_list)); ?>
                        </div>
                        <div id="department" style="<?php echo $department_style; ?>">
                             <?php $this->widget('ext.Picker', array('id' => 'picker_department', 'type' => 'depart', 'picked' => $department_list)); ?>
                        </div>
                <?php
                    }
                ?>

            </div>
    </div>
    </div>
</div>
<!-- 屏蔽完成节点短信提醒
<div class="clearfix">
    <div class="cell">
        <label>同时短信提醒</label>
        <div class="item">
            <div class="main">
                 <input id="sms_notice_0" value="0" <?php echo $sms_notice == 1 ?  '' : 'checked'; ?> type="radio" name="sms_notice">
                 <label for="sms_notice_0">否</label>
                 <input id="sms_notice_1" value="1" <?php echo $sms_notice == 1 ?  'checked' : ''; ?> type="radio" name="sms_notice">
                 <label for="sms_notice_1">是</label>
             </div>
        </div>
    </div>
</div>
  -->
<div class="actions">
    <input type="hidden" value="<?php echo $is_create; ?>" id="is_create" name="is_create" />
    <button type="button" id="start_node_save">确定</button>
    <button type="button" data-act="cancel" class="js-dialog-close">取消</button>
</div>

<?php $this->endWidget(); ?>

<script>
    jQuery(function($) {
        $("#notice_info :checkbox").click(function(){
            var tar = $(this).val();
            if(this.checked){
                $("#" + tar).show();
            }else{
                $("#" + tar).hide();
            }
        });
        $("#start_node_save").click(function(){
            var list = $("#notice_info input:checked");
            if(list.length == 0){
                $("#end_node .role, #end_node .user, #end_node .department, #end_node .node_name").val("").change();
                $("#end_node a").html("点此，设置审批完成后需通知的人员");
                $(".js-dialog-close").click();
                return false;
            }else{
                var user = [];
                var role = [];
                var department = [];
                var user_name = [];
                var role_name = [];
                var department_name = [];
                //处理选中的部门 用户 角色数据
                list.each(function(k,v){
                    if(this.value == 'user'){
                        $("input[name='user_id[]']").each(function(){
                            user.push(this.value);
                            user_name.push($(this).prev().text());
                        });
                    }
                    if(this.value == 'role'){
                        $("input[name='role_id[]']").each(function(){
                            role.push(this.value);
                            role_name.push($(this).prev().text());
                        });
                    }
                    if(this.value == 'department'){
                        $("input[name='depart_id[]']").each(function(){
                            department.push(this.value);
                            department_name.push($(this).prev().text());
                        });
                    }
                });
                if(role.length == 0 && user.length == 0 && department.length == 0){
                    $.flash('请选择角色，人员或者部门', 'warn');
                }else{
                    //角色ID 用户ID 部门ID 赋值到隐藏域
                    $("#end_node .role").val(role.join(',')).change();
                    $("#end_node .user").val(user.join(',')).change();
                    $("#end_node .department").val(department.join(',')).change();
                    //节点名称处理 并赋值到隐藏域
                    var role_str = role_name.join(',');
                    var department_str = department_name.join(',');
                    var user_str = user_name.join(',');
                    var names = [user_str, role_str, department_str].filter(function(name){
                        return !!name;
                    }).join(',');
                    $("#end_node .node_name").val(names).change();
                    var str ='审批完成后通知：(' + (names.length > 20 ? (names.substr(0 ,20) + '...') : names) + ")" ;
                    str += '<br/> 点击修改';
                    $("#end_node a").html(str).attr('title', names);
                    $(".js-dialog-close").click();
                }
            }
        });
    });
</script>
