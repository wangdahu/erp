<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'photo'); ?>
        <div class="item span12">
            <div class="main">
            <span class="hint">产品样式图片，120*90像素</span>
            <?php if ($model->photo):?>
            <img id="product_photo" src="<?=$model->photo?>" />
            <?php endif;?>
        <?php $this->widget('application.components.Upload', array(
                'id' => 'photo',
                'css' => 'styles.css',
                'postParams' => array('width' => 60, 'height' => 60, 'isTemporary' => true),
                'config' => array(
                    'maxfilesize' => 1024*1024,
                    'maxfiles' => 1,
                    'download' => 1, //是否提供下载功能(1表示提共下载功能,0表示不提供)
                    'app' => 'pss',
                    'pointName' => '上传图片',
                    'extensions' => CJSON::encode(Attach::$image_extension),
                    'finish' => 'js:function(i, file, response){callback.apply(this, arguments);}',
                )
            )
        );?>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'en_intro'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'en_intro'); ?>
           <span class="hint">英文介绍，如不设置则默认为产品介绍</span>
            </div>
            <?php echo $form->error($model->detail, 'en_intro'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'en_remark'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'en_remark'); ?>
           <span class="hint">英文备注，如不设置则默认为产品备注</span>
            </div>
            <?php echo $form->error($model->detail, 'en_remark'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'volume'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'volume'); ?>
            </div>
            <?php echo $form->error($model->detail, 'volume'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'gross_weight'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'gross_weight'); ?>
            </div>
            <?php echo $form->error($model->detail, 'gross_weight'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'weight'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'weight'); ?>
            </div>
            <?php echo $form->error($model->detail, 'weight'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'packaging'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'packaging'); ?>
            </div>
            <?php echo $form->error($model->detail, 'packaging'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'material'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'material'); ?>
            </div>
            <?php echo $form->error($model->detail, 'material'); ?>
        </div>
    </div>
</div>

<script>
function callback(i, file, response){
    var previewbox = $("#" + this.contaner_id).find("." + this.previewbox),
    preview = $("div[data-key=" + i + "]", previewbox);
    if (!response.error) {
        preview.remove(); 
        var new_attach = $("#"+this.contaner_id+" .new-attach");
        new_attach.empty();
        var template = [
                '<div class="each-item">',
                    '<span class="title">',
                        response.filename,
                        '<input type="hidden" name="'+this.postPrefix+'[path][]" value="'+response.filepath+'" />',
                        '<input type="hidden" name="'+this.postPrefix+'[name][]" value="'+response.filename+'" />',
                    '</span>',
                    '<a href="javascript:;" style="text-decoration:none;" class="file-delete idkin_icons">&nbsp;</a>',
                '</div>',
            ].join("");
    
        new_attach.append($(template));
        $("#product_photo").attr("src", response.filepath);
    } else {
        $.flash(response.message, "error");
    }
}
</script>