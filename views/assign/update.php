<?php 
$form = $this->beginWidget('ActiveForm', array(
    	'id' => 'assign-update-form',
    	'enableClientValidation' => true,
    ));


$form->clientOptions['afterValidate'] = "js:function(form, data, hasError) {
    var assignedPanel = $('#update_assigned_panel');
    if(assignedPanel.html() == ''){
        $.flash('请选择负责采购的产品', 'warn');
        return false;
    }else{
        $.post(form.attr('action'), form.serialize(), function(json){
            if(json.status == '1'){
                $.flash('修改成功', 'notice');
                location.href = location.href;
            }
            $('#assgin_close').click();
        }, 'json');
    }
    return false;
}";
?>
    <div class="clearfix">
        <div class="cell">
            <label>采购对象</label>
            <div class="item">
                <div class="main">
                    <?=$model->name;?>
                    <?=$form->error($model->buyAssignments[0], 'product_id');?>
                 </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell">
            <label>负责采购的产品</label>
            <div class="item">
                <div class="main">
                    <?=CHtml::dropDownList('productcate_id', '', array('' => '选择产品类别')+Product::getCateListData(), array('id' => 'update_productcate_id'));?>
                    <?=CHtml::dropDownList('product_id', '', array('' => '选择产品'), array('id' => 'update_product_id'));?>&nbsp;&nbsp;
                    <?=CHtml::link("清空", 'javascript:', array('id' => 'update_assign_clear'));?>
                    <div id="update_assigned_panel">
                        <?php $assingments = $model->buyAssignments;
                            foreach ($assingments as $ass){?>
                                <span style="padding-right: 20px;">
                                    <?=Product::model()->findByPk($ass->product_id)->name ?>
                                    <a style="margin-left: 5px" href="javascript:" class="js-remove-parent">X</a>
                                    <input name="product_id[]" type="hidden" value="<?php echo $ass->product_id?>">
                                </span>
                        <?  }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div style='margin: 5px 5px 0 0; float: right;'>
        <?php echo CHtml::button('确定', array('class' => 'button highlight', 'type' => 'submit'));?>
        <?php echo CHtml::button('取消', array('class' => 'button js-dialog-close', 'id' => 'assgin_close', 'type' => 'reset'));?>
    </div>
<?php $this->endWidget();?>

<script>
$(function(){
    var selProduct = $('#update_product_id')[0];
    $('#update_productcate_id').change(function() {
        $.get('/index.php?r=pss/product/cateList&cate_id=' + this.value, function(arr) {
            var html = new idk.Format();
            arr.forEach(function(opt) {
                html.push('<option value="${0}">${1}</option>', opt);
            });
            $(selProduct).html(html.toString());
        }, 'json');
    });
    var assignedPanel = $('#update_assigned_panel');
    $('#update_product_id').change(function() {
        var val = selProduct.value;
        if(val) {
            if(assignedPanel.find(':hidden[value=' + val + ']').length > 0) {
                return $.flash('产品已选择', 'warn');
            }
            assignedPanel.append(idk.format('<span style="padding-right: 20px;">${text}<a style="margin-left: 5px" href="javascript:" class="js-remove-parent">X</a><input name="product_id[]" type="hidden" value="${value}"></span>',
                                 selProduct.options[selProduct.selectedIndex]));
        }
    });
    $('#update_assign_clear').click(function() {
        assignedPanel.empty();
    });
    $('.js-remove-parent').live('click', function() {
        $(this).parent().remove();
    });
});
</script>