<?php
$form = $this->beginWidget('ActiveForm', array('id' => 'copy_flow','enableAjaxValidation'=>true));
?>
<div style="color:#666">
1.复制“<span style="color:#006A92"><?=$flow['name']?></span>”后，可根据需要将此审批流程用于其他表单的审批。<br/>
2.您可在下方对本流程进行重新命名和选择本流程将适用的表单。</div>

<div class="clearfix">
    <div class="cell">
        <label for="FlowGroup_tag" class="required">流程名称<span class="required">*</span></label>
        <div class="item">
            <div class="main">
                <input name="new_flow_name" id="new_flow_name" type="text" value="<?=$flow['name']?>" >
            </div>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <label for="FlowGroup_tag" class="required">适用表单<span class="required">*</span></label>
        <div class="item">
            <div class="main">
                <select id="current_form_name" name="current_form_name">
                    <?php 
//                        if(count($allforms)){
//                            foreach ($allforms as $v) {
//                                $selected = ($v['name'] == $flow['name'] ? 'selected=selected' : '');
//                                echo "<option {$selected} value='" . $v['form_name'] . "'>" . $v['name'] . "</option>";
//                            }
//                        }
                    ?>
                    <?php 
                        if(count(FormFlow::getLeftMenuList())){
                            foreach (FormFlow::getLeftMenuList() as $k => $v) {
                                $selected = ($k == $form_name ? 'selected=selected' : '');
                                echo "<option {$selected} value='" . $k . "'>" . $v . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="actions">
    <button type="submit">提交</button>
    <button type="button" data-act="cancel" class="js-dialog-close">取消</button>
</div>

<?php $this->endWidget(); ?>

<script>
    jQuery(function($) {
        $("#copy_flow").submit(function(){
            if($.trim($("#new_flow_name").val()) == ''){
                $.flash('请输入流程名称', 'warn');
                return false;
            }
        });
    });
</script>
