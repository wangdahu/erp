<div style='width: 660px; height: 320px; overflow: auto;'>
<?php 
$form = $this->beginWidget('ActiveForm', 
    array(
        'id' => 'billing-form', 
        'action' => Yii::app()->createUrl($this->route),
    ));
    
?>
<div class="main-search-forms">
    <div class="clearfix">
        <div class="cell span8">
            <?=$form->labelEx($model, 'no');?>
            <div class="item">
                <div class="main">
                    <?=$form->textField($model, 'no');?>
                </div>
                <?=$form->error($model,'no');?>
            </div>
        </div>
        <div class="cell span6">
            <?=$form->labelEx($model, 'created');?>
            <div class="item">
                <div class="main">
                    <?=date('Y-m-d H:i', $model->created);?>
                </div>
                <?=$form->error($model,'created');?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <label><?=$form->labelEx($model, 'type');?></label>
            <div class="item">
                <div class="main">
                    <?=$form->radioButtonList($model, 'type', $model->typeOptions, array('separator' => '&nbsp;'));?>
                </div>
                <?=$form->error($model, 'type');?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span7">
            <?=$form->labelEx($model, 'balance_type');?>
            <div class="item">
                <div class="main">
                    <?=$form->dropDownList($model, 'balance_type', $model->balanceTypeOptions, array('class' => 'span2'));?>
                </div>
                <?=$form->error($model, 'balance_type');?>
            </div>
        </div>
        <div class="cell span8">
            <?=$form->labelEx($model, 'cheque');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model, 'cheque');?>
                </div>
                <?=$form->error($model, 'cheque');?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?=$form->labelEx($model, 'partner_name');?>
            <div class="item">
                <div class="main">
                    <?=$form->dropDownList($model, 'partner_type', $model->partnerTypeOptions, array('class' => 'span2'));?>
                    <?=$form->textField($model, 'partner_name', array('readonly' => true));?>
                    <?=$form->hiddenField($model, 'partner_id');?>
                    <a id="partner-search" data-id="select-partner" class="js-dialog-link" href="<?=$this->createUrl('/erp/customer/popup')?>">查找</a>
                </div>
                <?=$form->error($model, 'partner_id');?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?=$form->labelEx($model, 'items');?>
            <div class="item span13">
                <div id="billing_item">
                <?php 
                $this->widget('zii.widgets.grid.CGridView', array(
                    'id'=>'billing-items',
                    'dataProvider'=>new CArrayDataProvider($items),
                    'template'=>'{items}',
                    'columns'=>array(
                        array('header' => '序号', 'value' => '$row+1', 'headerHtmlOptions'=>array('class'=>'span1'), ),
                        array('header' => '收支科目', 'value' => 'CHtml::activeDropDownList($data, "[$row]type", array())',
                                'type'=>'raw', 'headerHtmlOptions'=>array('style'=>'width:80px;'),),
                        array('header' => '金额', 'value' => 'CHtml::activeTextField($data, "[$row]price", array("class"=>"span2"))', 'type' => 'raw', 'headerHtmlOptions'=>array('class'=>'span2'), ),
                        array('header' => '备注', 'value' => 'CHtml::activeTextField($data, "[$row]remark", array("class"=>"span6"))', 'type' => 'raw',),
                        array('header' => '操作', 'value' => '""', 'headerHtmlOptions'=>array('class'=>'span1'), ),
                    ),
                ));?>
                </div>
                <a id="add_item" href="javascript:">添加更多收支明细</a>
                <?php echo CHtml::dropDownList('item_options', null, $items[0]->typeOptions, array('style'=>'display:none'));?>
            </div>
        </div>
    </div>
    
    <div class="actions" style='text-align: right; padding-right: 5px;'>
        <input name="submit_value" id="submitValue" type="hidden" />
        <button type="submit" class="highlight">确定</button>
        <button type="reset" class="js-dialog-close">取消</button>
    </div>
</div>
<?php $this->endWidget();?>
</div>

<script>
function populateSupplierData(data){
    populateData(data);
}
function populateCustomerData(data){
    populateData(data);
}
function populateData(data){
    $("#Billing_partner_name").val(data.name);
    $("#Billing_partner_id").val(data.id);
    $('#select-partner').dialog('close');
}

$(function(){
    var panel = $("#billing_item"),
        urls = ["<?=$this->createUrl('/erp/customer/popup')?>", "<?=$this->createUrl('/erp/supplier/popup')?>"];
    $(":radio[name='Billing[type]']").change(function(){
        var row = $($('tbody', panel)[0].rows[0]), selector = this.value == '0' ? ':lt(3)' : ':gt(2)';
        row.find('select').html($('#item_options option').filter(selector).clone());
        row.nextAll().remove();
    }).filter(':checked').change();
    
    $("select[name='Billing[partner_type]']").change(function(){
        $("#partner-search").attr('href', urls[this.value]);
    });
    function resetIndex(row, index) {
        row.find('input, select').each(function() {
            this.name = this.name.replace(/\d+(?=\]\[\w+\]$)/g, index);
        });
    }
    
    function resetRowNumber() {
        $($('tbody', panel)[0].rows).each(function() {
            this.cells[0].textContent = this.rowIndex;
            resetIndex($(this), this.rowIndex-1);
        });
    }
    // add new item 
    $("#add_item").click(function() {
        var tbody = $('tbody', panel)[0],
            len = tbody.rows.length,
            newRow = tbody.rows[len - 1].cloneNode(true);
        newRow.cells[0].textContent = len + 1;
        newRow.cells[newRow.cells.length - 1].innerHTML = '<a class="delete" href="javascript:">&nbsp;</a>'
        $(newRow).find("select, input").val("");
        resetIndex($(newRow).appendTo(tbody), len);
    });
    // remove row
    panel.delegate('.delete', 'click', function(){
        $(this).closest('tr').remove();
        resetRowNumber();
    });
});
</script>
