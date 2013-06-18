<?php
$form = $this->beginWidget('ActiveForm', array('id' => 'node_form','enableAjaxValidation'=>true));
?>

<div class="clearfix">
    <div class="cell">
        确定环节审批人
        <span class="required">*</span>
        &nbsp; 请在下方选择当前环节具体的审批人姓名或角色，如选择两人以上可勾选会签功能
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <div class="item span13">
            <div class="main">
                <span style="padding-left:110px;" id="notice_info">
                    <?php
                        if($is_create){
                    ?>
                            <input id="department_manage" value="department_manage" type="checkbox" name="department_manage" />
                            <label for="department_manage">申请人所在部门的部门经理</label>
                            
                            <input id="notice_info_role" value="role" type="checkbox" name="notice_info_role" />
                            <label for="notice_info_role">指定角色</label>
                            
                             <input id="notice_info_user" value="user" type="checkbox" name="notice_info_user" />
                            <label for="notice_info_user">指定人员</label>
                    <?php
                    }else{
                        $user_checked = (is_array($user_list) && count($user_list)) ? 'checked' : '';
                        $role_checked = (is_array($role_list) && count($role_list)) ? 'checked' : '';
                        $department_manage_checked = (is_array($department_manage_list) && count($department_manage_list)) ? 'checked' : '';
                    ?>
                            <input id="department_manage" value="department_manage" type="checkbox" name="department_manage" <?php echo $department_manage_checked; ?> />
                            <label for="department_manage">申请人所在部门的部门经理</label>
                            
                            <input id="notice_info_role" value="role" type="checkbox" name="notice_info_role" <?php echo $role_checked; ?> />
                            <label for="notice_info_role">指定角色</label>
                            
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
            <div class="main" style="padding-left:110px;overflow-y:auto;height: 300px; width:450px">
                <?php
                    if($is_create){
                ?>
                        <div id="role" style="display:none;">
                            <?php $this->widget('ext.Picker', array('id' => 'picker_role', 'type' => 'role', 'picked' => array())); ?>
                        </div>
                        
                        <div id="user" style="display:none;">
                            <?php $this->widget('ext.Picker', array('id' => 'picker_user', 'type' => 'user', 'picked' => array())); ?>
                        </div>
                        
                <?php
                    }else{
                        $user_style = (is_array($user_list) && count($user_list)) ? '' : 'display:none';
                        $role_style = (is_array($role_list) && count($role_list)) ? '' : 'display:none';
                     ?>
                        <div id="user" style="<?php echo $user_style; ?>">
                            <?php $this->widget('ext.Picker', array('id' => 'picker_user', 'type' => 'user', 'picked' => $user_list)); ?>
                        </div>
                        <div id="role" style="<?php echo $role_style; ?>">
                            <?php $this->widget('ext.Picker', array('id' => 'picker_role', 'type' => 'role', 'picked' => $role_list)); ?>
                        </div>
                <?php
                    }
                ?>
                
            </div>
    </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <label>需要会签</label>
        <div class="item">
            <div class="main">
                 <input id="need_all_allowed_0" value="0" <?php echo $is_all == 1 ?  '' : 'checked'; ?> type="radio" name="need_all_allowed">
                 <label for="Node_need_all_allowed_0">否</label>
                 <input id="Node_need_all_allowed_1" value="1" <?php echo $is_all == 1 ?  'checked' : ''; ?> type="radio" name="need_all_allowed">
                 <label for="Node_need_all_allowed_1">是</label>
                 <span style="color:#666666;">（会签：需当前环节所有审批人审批通过后，才可流转至下一审批环节）</span>
             </div>
        </div>
    </div>
</div>

<div class="actions">
    <input type="hidden" value="<?php echo $is_create ?>" id="is_create" name="is_create" />
    <input type="hidden" value="<?php echo $div_id ?>" id="div_id" name="div_id" />
    <input type="hidden" value="<?php echo $modify ?>" id="modify" name="modify" />
    <button type="button" id="node_save">确定</button>
    <button type="button" data-act="cancel" class="js-dialog-close">取消</button>
</div>

<?php $this->endWidget(); ?>

<script>
    jQuery(function($) {
        $("#notice_info :checkbox").click(function(e){
            var tar = e.target.value;
            if(tar != 'department_manage'){
                if(this.checked){
                    $("#" + tar).show();
                }else{
                    $("#" + tar).hide();
                }
            }
        });
        $("#node_save").click(function(){
            var list = $("#notice_info input:checked");
            if(list.length == 0){
                $.flash('请选择角色或者人员', 'warn');
                return false;
            }else{
                var user = [];
                var role = [];
                var user_name = [];
                var role_name = [];
                var department_manage = [];
                var department_manage_name = [];
                //处理选中的用户 角色数据
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
                    if(this.value == 'department_manage'){
                        department_manage.push('-1');
                        department_manage_name.push('申请人所在部门的部门经理');
                    }
                });

                if(role.length == 0 && user.length == 0 && department_manage.length == 0){
                    $.flash('请选择角色或者人员', 'warn');
                    return false;
                }else{
                    var modify = $("#modify").val();
                    var div_id = $('#div_id').val();
                    var tmp = div_id.split('_');
                    //拼接节点名称
                    var role_str = role_name.join(',');
                    var department_manage_str = department_manage_name.join(',');
                    var name = [user_name.join(','), role_str, department_manage_str].filter(function(item){
                        return !!item;
                    }).join(',');
                    var str = name.length > 20 ? (name.substr(0 ,20) + '...') : name;
                    str += '<br/> 点击修改';
                    //动态创建html
                    if(modify == 0){//添加下一节点
                        var html = '<div id="' + div_id + '"><div class="rectangle bg_node" data-node="node" style="float:left;"><a href="javascript:void(0);">' + str + '</a></div><div class="notice"></div><div class="arrows"></div><input type="hidden" value="" class="role"><input type="hidden" value="" class="user"><input type="hidden" value="" class="department"><input type="hidden" value="" class="is_all"><input type="hidden" value="" class="node_name"><input type="hidden" value="" class="department_manage"/></div>'
                        var prev_id = parseInt(tmp[1]) - 1;
                        $("#node_" + prev_id).after(html);
                    }else{//修改节点 直接覆盖
                        $("#" + div_id + " a").html(str).attr('title',name);
                    }

                    //重置中间节点ID 保证节点顺序
                    $("div[id^='node_']").each(function(k,v){
                        $(this).prop('id','node_' + (k + 1));
                    });

                    //角色ID 用户ID 是否会签 赋值到隐藏域
                    $("#" + div_id + " .role").val(role.join(',')).change();
                    $("#" + div_id + " .user").val(user.join(',')).change();
                    $("#" + div_id + " .user").val(user.join(',')).change();
                    $("#" + div_id + " .department_manage").val(department_manage.join(',')).change();
                    $("#" + div_id + " .node_name").val(name).change();
                    var need_all_allowed = $("input[name='need_all_allowed']:checked").val();
                    $("#" + div_id + " .is_all").val(need_all_allowed).change();

                    //动态链接处理
                    $("#" + div_id + " .notice").html('<a href="javascript:void(0)" data-act="add">添加下一审批人</a><br/><a data-act="del" href="javascript:void(0)">删除此审批人</a>');

                    if($(".notice a").length == 2){
                        $("a[data-act='del']").addClass('del_node');
                    }else{
                        $("a[data-act='del']").removeClass('del_node');
                    }
                    $(".js-dialog-close").click();
                }
            }
        });
    });
</script>
