<div style='margin: 10px 0 10px 0; color: #CCC;'>请在下方设置各类产品的采购负责人，设置后，采购人在采购计划中会看见自己负责采购的产品。</div>
<?php $form = $this->beginWidget('ActiveForm', array('id' => 'BuyAssignment',));?>
    <div class="more-info" style="display: block;">
        <div class="clearfix">
            <div class="cell">
                <label style='width: 100px;'>选择产品采购人</label>
                <div class="item">
                    <div class="main">
                        <?php echo CHtml::checkBoxList('type', 1, BuyAssignment::getBuyerType(), array('separator' => '&nbsp;'));?>
                     </div>
                </div>
            </div>
        </div>
        
        <div class="clearfix">
            <div class="cell">
                <?php $this->widget('ext.Picker', array('id' => 'assign_role', 'type' => 'role', 'style'=> "margin-top:10px;"));?>
                <?php $this->widget('ext.Picker', array('id' => 'assign_user', 'type' => 'user', 'style'=> "margin-top:10px; display:none"));?>
            </div>
        </div>
        
        <div class="clearfix">
            <div class="cell">
                <label for="CrmContact_type" style='width: 100px;'>负责采购的产品</label>
                <?=CHtml::dropDownList('productcate_id', null, array('' => '选择产品类别')+Product::getCateListData());?>
                <?=CHtml::dropDownList('product_id', null, array('' => '选择产品'));?>&nbsp;&nbsp;
                <?=CHtml::link("清空", 'javascript:', array('id' => 'assign_clear'));?>
                <div id="assigned_panel"></div>
            </div>
        </div>
        
        <div style='margin: 5px 5px 0 0; float: right;'>
            <?php echo CHtml::button('确定并继续分配', array('class' => 'button highlight', 'type' => 'submit'));?>
            <?php echo CHtml::button('确定并返回', array('class' => 'button highlight', 'type' => 'submit'));?>
            <?php echo CHtml::button('取消', array('class' => 'button js-dialog-close', 'type' => 'reset'));?>
        </div>
    </div>
<?php $this->endWidget();?>



<script>
$('[name="type[]"]').change(function() {
    $("#assign_" + ["user", "role"][this.value]).toggle(this.checked);
});
var selProduct = $('#product_id')[0];
$('#productcate_id').change(function() {
    $.get('/index.php?r=erp/product/cateList&cate_id='  + this.value, function(arr) {
        var html = new idk.Format();
        arr.forEach(function(opt) {
            html.push('<option value="${0}">${1}</option>', opt);
        });
        $(selProduct).html(html.toString());
    }, 'json');
});
var assignedPanel = $('#assigned_panel');
$('#product_id').change(function() {
    var val = selProduct.value;
    if(val) {
        if(assignedPanel.find(':hidden[value=' + val + ']').length > 0) {
            return $.flash('产品已选择', 'warn');
        }
        assignedPanel.append(idk.format('<span style="padding-right: 20px;">${text}<a style="margin-left: 5px" href="javascript:" class="js-remove-parent">X</a><input name="product_id[]" type="hidden" value="${value}"></span>',
                                  selProduct.options[selProduct.selectedIndex]));
    }
});
$('#assign_clear').click(function() {
    assignedPanel.empty();
});
$('.js-remove-parent').live('click', function() {
    $(this).parent().remove();
});
</script>
