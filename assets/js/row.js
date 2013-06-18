// 功能：Customer添加或删除一个电话、手机等

// 添加一行
function addRow(className, msg, maxRows){
    var rows = $("."+ className).length;
    if(rows > maxRows){
        $.alert(msg);
    }else{
        var rowHtml = $("." + className + ":hidden");
        rowHtml.after(rowHtml.clone()).show();
        resetIndex(rowHtml, $("."+className+":visible").length);
    }
}


// 删除一行
function removeRow(obj, className){
    var row = obj.closest("." + className);
    row.remove();
    $("."+className+":visible").each(function(i) {
        resetIndex($(this), i);
    });
}

//判断重复，如果有重复的将提示及清空
function checkRow(obj, selector, msg){
    var val = obj.val(), 
    row = obj.closest(selector);
    valid = true;
    row.siblings(selector + ":visible").find('input').each(function(i){
        return (valid = !(val == this.value));
    });
    obj.data('invalid', !valid);
    if(!valid) {
        $.flash(msg, "warn");
        obj.focus();
    }
}

function resetIndex(element, index) {
    element.find('input, select').each(function() {
        this.name = this.name.replace(/\d+(?=\]\[\w+\]$)/g, index);
    });
}



//默认加载事件，如果有特殊处理的，需要重新定义下列事件，例如：customer/view/contact/view.php
$(function() {

    // 添加手机号码
    $('.js-add-mobile').click(function(){
        addRow("js-mobile", "只能添加5个手机号码", 5);
    });

    // 添加电话号码
    $('.js-add-phone').click(function(){
        addRow("js-phone", "只能添加5个电话号码", 5);
    });

    // 删除：手机号码
    $('.js-remove-mobile').live('click', function(){
        removeRow($(this), "js-mobile");
    });

    // 删除：电话号码
    $('.js-remove-phone').live('click', function(){
        removeRow($(this), "js-phone");
    });

    // 检测：不允许重复值
    $('.js-contact-box').delegate("input", 'change', function(){
        var data = [{cls: ".js-mobile", name: "手机"}, {cls: ".js-phone", name: "电话"}][/phone/.test(this.name) - 0];
        checkRow($(this), data.cls, "请不要填写相同的" + data.name + "号码");
    });
});
