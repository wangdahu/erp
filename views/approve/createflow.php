<?php
$this->renderPartial('../_settingTop');
$this->pageTitle = '进销存审批事务设置';
Yii::app()->clientScript->registerScriptfile('/source/oa/js/unload-confirm.js');
$options = '';

if (count($flows)) {
    foreach ($flows as $flow) {
        if ($is_edit && $flow['flow_id'] == $flow_id) {
            $current_flow = $flow;
        }
        $options .= "<option value='{$flow['flow_id']}'>{$flow['name']}</option>";
    }
}
?>

<style>
    .del_node {color:#D6D6D6;}
    .rectangle {min-height:50px; width:150px; padding-top: 10px; padding-bottom: 10px; text-align:center; border:1px solid #2F7EA2;}
    .bg {background-color: #DDD;}
    .bg_node {background-color: #EFF6FC;}
    .notice {margin-left:180px; color:#666666;}
    .arrows {width:150px;height:54px; background: url('<?=$this->module->assetsUrl;?>/icon/arrow.png') no-repeat center 0; clear:left;}
</style>

<div>
    <?php $this->renderpartial('../_approveLeft');?>
    
    <div style="margin-left: 190px;">
        <div class="main-content">
            <div class="main-title-big radius-top" style='font-weight: bold; margin-bottom: 5px; font-size: 20px;'>
                <label>
                    <?php $left_menu_name = FormFlow::getLeftMenuList();
                    echo $is_edit ? ($left_menu_name[$form_name].'&nbsp;流程编辑') : ($left_menu_name[$form_name].'&nbsp;新增流程'); ?>
                </label>
            </div>
            <div class="padding-top">
                <form id="flow_form">
                    <div class="clearfix">
                        <div class="cell span8">
                            <label>流程名称</label>
                            <div class="item">
                                <div class="main">
                                    <input type="text" value="<?php echo $is_edit ? $current_flow['name'] : ''; ?>" name="flow_name" id="flow_name" />
                                </div>
                            </div>
                        </div>
                        <div class="cell">
                            <p class="hint">流程名称用于流程的区分及管理，您可按流程特征命名</p>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="cell">
                            <label>流程设置</label>
                            <div class="item">
                                <div class="main">
                                    <select id="exists_flow" style="min-width:160px">
                                        <option value="0">请选择</option>
                                        <?php echo $options; ?>
                                    </select>
                                </div>
                                <div class="cell">
                                    <p class="hint" style='margin-left: 54px;'>您可以直接点击下面的图框进行设置，或者复制已有审批流程</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="clearfix">
                        <div class="cell ">
                            <label>&nbsp;</label>
                            <div class="item span14">
                                <div class="main">
                                    <div class="empty-split" style="margin-top:10px;"></div>
                                    <div id="flow_html">
                                        <?php
                                        if ($is_edit) {
                                            $nodes = ErpFlow::getNodeByFlowId($flow_id);
                                            $this->renderPartial('_flow_data', array('is_edit' => $is_edit, 'nodes' => $nodes));
                                        } else {
                                            $this->renderPartial('_flow_data', array('is_edit' => $is_edit));
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="span20 clearfix actions">
                        <button type="submit" id="save_flow" class="highlight">保存</button>
                    </div>
                    
                    <input type="hidden" name="current_group_id" value="0" id="current_group_id" />
                    <input type="hidden" name="current_form_name" value="<?php echo $form_name?>" id="current_form_name" />
                    <input type="hidden" name="current_flow_id" value="<?php echo isset($flow_id) ? $flow_id : ''; ?>" id="current_flow_id" />
                </form>
            </div>
            <br style="clear:both;" />
        </div>
    </div>
</div>

<script>
    jQuery(function($) {
        var unloadcfm = new UnloadConfirm({form:"#flow_form"});
        //流程复制
        $("#exists_flow").change(function(e){
            var flow_id = e.target.value;
            var current_flow_id = $("#current_flow_id").val();
            if(flow_id == 0 && current_flow_id > 0){
                flow_id = current_flow_id;
            }
            //根据已有的流程ID 获取html
            $.post("index.php?r=erp/approve/getflowhtml&flow_id=" + flow_id, function(data){
                if(data != ''){
                    $("#flow_html").html(data);
                    //流程编辑 删除默认灰色处理
                    if($(".notice a").length == 2){
                        $(".notice a[data-act='del']").addClass('del_node');
                    }
                }
            });
        });
        //流程编辑 删除默认灰色处理
        if($(".notice a").length == 2){
            $(".notice a[data-act='del']").addClass('del_node');
        }
        //添加 删除 下一审批人
        $(".notice a").live('click',function(){
            var act = $(this).data('act');
            var div_id = $(this).parent().parent().prop('id');
            if('add' == act){//添加下一结点
                var div_arr = div_id.split('_');
                var tmp_id = (parseInt(div_arr[1]) + 1);
                var div_id = div_arr[0] + '_' + tmp_id;
                $.dialog({
                    url: 'index.php?r=erp/approve/createnode&user=&role=&department=&department_manage=&div_id=' + div_id + '&modify=0&is_all=0',
                    id: "js-create-node",
                    title: '添加第' + tmp_id + '审批人'
                });
            }else{//移除当前结点
                if($(".notice a").length != 2){
                    $("#" + div_id).remove();
                    //重置中间节点ID 保证节点顺序
                    $("div[id^='node_']").each(function(k,v){
                        $(this).prop('id','node_' + (k + 1));
                    }); 
                }
                if($(".notice a").length == 2){
                    $(".notice a[data-act='del']").addClass('del_node');
                }
            }
        });
        //点击开始 中间 结束 节点
        $(".rectangle").live('click', function(){
            var node = $(this).data('node');
            var parent = $(this).parent();
            var user = parent.find('.user').val();
            var role = parent.find('.role').val();
            var department = parent.find('.department').val();
            switch(node){
                case "start":    //开始节点
                    var title = '设置流程适用人';
                    var id = "js-create-start-node";
                    var url = 'index.php?r=erp/approve/createstartnode&user=' + user + '&role=' + role + '&department=' + department;
                    break;
                case "node":     //中间节点
                    var is_all = parent.find('.is_all').val();
                    var department_manage = parent.find('.department_manage').val();
                    var div_id = $(this).parent().prop('id');
                    var tmp = div_id.split('_');
                    var title = '添加第' + tmp[1] + '审批人';
                    var id = "js-create-node";
                    var url = 'index.php?r=erp/approve/createnode&user=' + user + '&role=' + role + '&department=' + department + '&div_id=' + div_id + '&modify=1&is_all=' + is_all + '&department_manage=' + department_manage;
                    break;
                case "end":      //结束节点
                    var sms_notice = parent.find('.sms_notice').val();
                    var title = '设置审批完成后需通知的人员';
                    var url = 'index.php?r=erp/approve/createendnode&user=' + user + '&role=' + role + '&department=' + department + '&sms_notice=' + sms_notice;
                    var id = "js-create-end-node";
                    break;
            };
            $.dialog({
                url: url,
                id: id,
                title: title
            });
        });
        //表单提交 
        $("#flow_form").bind('submit', function(){
            var flow_name = $("#flow_name").val();
            if($.trim(flow_name) == ''){
                $.flash('请输入流程名称', 'warn');
                return false;
            }
            //结点验证
            var start_node_data = checkHidden('start_node');
            if(start_node_data == false){
                $.flash('请设置流程适用人', 'warn');
                return false;
            }
            var end_node_data = checkHidden('end_node');
            if(end_node_data == false){
                end_node_data = '';
                //$.flash('请设置审批完成后需通知的人员');
                //return false;
            }
            var approve_node_data = [];
            var mark = false;
            $("div[id^='node_']").each(function(k,v){
                var div = $(this).prop('id');
                var node_data = checkHidden(div);
                if(node_data == false){
                    mark = true;
                    $.flash('请设置审批人', 'warn');
                    return false;
                }else{
                    approve_node_data.push(node_data);
                }
            });
            if(mark){
                return false;
            }
            //数据提交
            var group_id = $("#current_group_id").val();
            var form_name = $("#current_form_name").val();
            var flow_id = $("#current_flow_id").val();
            var post_data = {'start_node' : start_node_data, 'end_node' : end_node_data, 'approve_node' : approve_node_data, 
                'group_id' : group_id, 'flow_name' : flow_name, 'form_name' : form_name, 'flow_id' : flow_id};
            $.post("index.php?r=erp/approve/insertflow", post_data, function(data){
                if(data == 1){
                    unloadcfm.sleep(true);
                    location.href = '/index.php?r=erp/approve/index&form_name='+form_name;
                }else{
                    $.flash('添加失败', 'error');
                }
            });
            return false;
        });
        //检查隐藏域的值
        function checkHidden(div){
            var flag = false;
            var data = [];
            var node_name = '';
            var is_all = 0;
            var sms_notice = 0;
            $("#" + div + " input[type='hidden']").each(function(){
                var value = $(this).val();
                if(value != ''){
                    var tar = $(this).prop('class');
                    //屏蔽完成节点短信提醒
                    //if(tar == 'sms_notice'){
                    //sms_notice = $(this).val();
                    //}else 
                    if(tar == 'node_name'){//节点名称
                        node_name = $(this).val();
                    }else if(tar == 'is_all'){//节点是否会签
                        is_all = $(this).val();
                    }else{//用户ID 角色ID 部门ID 数据
                        flag = true;
                        data.push(tar + '|' +  value);
                    }
                }
            });
            //返回节点数据对象
            return flag == false ? false : {'data' : data, 'node_name' : node_name, 'is_all' : is_all};
        }
    });
</script>
