<div class="form" style='overflow: auto; height: 480px; width: 620px;'>

<?php 
$form=$this->beginWidget('ActiveForm', array(
	'id'=>'product-form',
)); 
if (isset($_GET['popup']) && $_GET['popup'] == 1){
    $form->clientOptions['afterValidate'] = "js:function(form, data, hasError) {
        $.post(form.attr('action'), form.serialize(), function(json){
            if(json.status){
                var data = $.extend(json.data.product, {brand:json.data.brand.name, cate:json.data.cate.name, unit:json.data.unit.name});
                populateData([data]);
                //关闭弹出层
                $('.js-dialog-close').click();
            }
        }, 'json');
        return false;
    }";
}

$tabs = array(
    'base' => array('title' => '基本资料', 'view' => 'tabViews/base'),
    'price' => array('title' => '产品标价', 'view' => 'tabViews/price'),
    'param' => array('title' => '产品参数', 'view' => 'tabViews/param'),
);
$this->widget('system.web.widgets.CTabView', array(
    'id' => 'product-index-tab',
    'tabs' => $tabs,
    'viewData' => array('model' => $model, 'form' => $form),
));
?>
    <?php if ($model->isNewRecord):?>
    <hr/>
    <div>
    <div class="clearfix" style="padding-left:6px;">
        <div class="cell">
            <?php echo $form->labelEx($stock,'quantity'); ?>
            <div class="item span12">
                <div class="main" style="clear:left;padding-bottom:4px;">
                <?php echo $form->textField($stock, '[0]quantity', array('class' => 'mini')); ?>
                <?php echo $form->dropDownList($stock, '[0]storehouse_id', array('' => '选择仓库') + Chtml::listData($houses, 'id', 'name'), array('class'=>'stock')); ?>
                <a href="javascript:" id="add_stock" >添加</a>
                </div>
                <?php echo $form->error($stock,'quantity'); ?>
            </div>
        </div>
    </div>
    </div>
    <?php endif;?>
    
    <div class="actions">
        <?php echo CHtml::htmlButton('确定', array('type' => 'submit', 'class' => 'highlight')); ?>
        <?php echo CHtml::htmlButton('取消', array('class' => 'js-dialog-close')); ?>
    </div>
<?php
$this->endWidget(); 
?>
</div>

<script>
(function() {
$('#product-form .highlight').live('click',function(){
    var quantitys = $('.mini'), stocks =$('.stock'), bool = true;
    $.each(quantitys, function(k,v){
        if(this.value != '' && this.value != '0' && stocks[k].value == ''){
            bool = false;
        }
    })
    if(!bool){
        $.alert('请选择仓库');
        return false;
    }
});

function resetIndex(row) {
    var index = row.index() - 1;
    row.find('input, select').each(function() {
        this.name = this.name.replace(/\d+(?=\]\[\w+\]$)/, index);
    });
}
var selector = '.main';
var optLength = null;
$('#add_stock').click(function() {
    var row = $(this).closest(selector),
        newRow = row.clone();

    if($(this).hasClass('disabled')) { return; }
    var newSelect = newRow.find('select'),
        selectedSelects = row.parent().find('select').filter(function() { return !!this.value; });
    if(optLength === null) {
        optLength = newSelect[0].options.length;
    }
    $.each(selectedSelects, function() {
        newSelect.find('option[value=' + this.value + ']').remove();
    });
    newRow.find('input').val('');
    newRow.find('#add_stock').attr('class', 'delete').html('&nbsp;');
    row.parent().append(newRow);
    resetIndex(newRow);
    $(this).toggleClass('disabled', row.siblings().length == optLength - 1);
})
.closest(selector).parent().delegate('.delete', 'click', function(){
    var row = $(this).closest(selector),
        nextRows = row.nextAll();
    $('#add_stock').toggleClass('disabled', row.siblings().length - 1 == optLength - 1);
    row.remove();
    nextRows.each(function() {
        resetIndex($(this));
    });
}).delegate('select', 'change', function() {
    var select = $(this), oldValue = select.data('value') || '';
    var selects = select.closest(selector).parent().find('select').not(select);
    if(oldValue) {
        selects.append('<option value="' + oldValue + '">' + select.find('option[value=' + oldValue + ']').text() + '</option>');
    }
    if(this.value) {
        selects.find('option[value=' + this.value + ']').remove();
    }
    select.data('value', this.value);
});
})();

var form = $('#product-form');
function voluationCate(data){
    $('#Product_cate_id', form).append(
        "<option value='"+data.id+"' selected >"+data.name+"</option>"
    );
}

function voluationUnit(data){
    $('#Product_unit_id', form).append(
        "<option value='"+data.id+"' selected >"+data.name+"</option>"
    );
}

function voluationBrand(data){
    $('#Product_brand_id', form).append(
        "<option value='"+data.id+"' selected >"+data.name+"</option>"
    );
}
</script>
