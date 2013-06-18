<!-- 产品目录搜索条件start -->
<?php $form = $this->beginWidget('ActiveForm', array(
          'action' => Yii::app()->createUrl($this->route),
          'method' => 'get',
      ));?>

<div class="more-info" style="padding-top: 10px; display: block;">
    <div class="clearfix">
        <div class="cell span10">
            <label for="CrmContact_type">产品品牌</label>
            <div class="item">
                <div class="main">
                    <?php echo $form->dropDownList($product, 'brand_id', array('' => '请选择品牌')+Product::getBrandListData());?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell">
            <label>录入时间</label>
            <div class="item">
                <div class="main">
                	<?php $request = Yii::app()->request;?>
                     <?php 
                    $dates = $product->datePatternOptions;
                    $dates[3] .= ' '.$form->textField($product,'start_date', array('class' => 'js-datepicker', 'data-group' => 'created', 'data-type' => 'start')). ' ';
                    $dates[3] .= $form->textField($product,'end_date', array('class' => 'js-datepicker', 'data-group' => 'created'));
                    echo $form->radioButtonList($product,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix">
    <div class="cell">
        <label>关键词</label> 
        <div class="item">
            <div class="main">
                <?php echo $form->textField($product, 'name', array('placeholder' => "请输入产品名称、品牌", 'size' => 40));?>
                <input class="button" type="submit" name="yt0" value="查找">
                <a class="search-button" href="javascript:void(0);">&gt;&gt;详细条件搜索</a>
                <input type="hidden" value="1" name="isShow" id="isShow">
             </div>
        </div>
    </div>
</div>
<!-- 产品目录搜索条件end -->
<?php $this->endWidget();?>