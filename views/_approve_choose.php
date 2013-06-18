<?php $leftMenuName = FormFlow::getLeftMenuList();
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl . '/js/_chk.js');
?>

    <div class="clearfix" style='margin-top: 10px;'>
        <div class="cell span25">
            <label for="approve_flow" class="required">审批流程 <span class="required">*</span></label>
            <div class="item">
                <div class="main">
                    <?php 
                    if(count($model->getFlows())) :
                        foreach ($model->getFlows() as $k=>$flow):
                            if ($flow->isApply):
                                $data[$flow->flow_id] = $flow->name . '&nbsp;（'.implode(' → ', $flow->getApproveNodes()).'）';
                            endif;
                        endforeach;
                        
                        if(empty($data)){
                            echo '<label>无</label>';
                            echo $form->hiddenField($model, 'approval_id');
                        }else{
                            echo $form->radioButtonList($model, 'approval_id', $data);
                        }
                        echo $form->error($model, 'approval_id');
                    else :?>
                        <label>无</label>
                        <?php echo $form->hiddenField($model, 'approval_id');
                        echo $form->error($model, 'approval_id');?>
                        <script>
                        jQuery(function($){
                            var opt = {
                                message: '<div style="line-height:200%;"><span class="red">"<?=$leftMenuName[get_class($model)] ?>"</span> 没有建立审批流程，不能发布事务。</div>',
                                title: '提示',
                                okbtn: '马上 新建审批流程 >>',
                                createPssFlow:function(){
                                    var link = "<?php echo Yii::app()->createUrl('/pss/approve/index', array('form_name' => get_class($model))); ?>";
                                    window.location= link;
                                }
                            }
                            $.alert(opt.message, opt.createPssFlow, opt.title, opt.okbtn);
                        });
                        </script>
                    <?endif;?>
                </div>
            </div>
        </div>
    </div>
    
<script>
jQuery(function($) {
    // 页面载入完成后就提示
    _init_body._ready_notice("<?php echo Yii::app()->createUrl('/pss/approve/hasRoleWhichFlowEveryNode'); ?>");
});
</script>