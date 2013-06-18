var _init_body = {
    // 页面载入完成后就提示
    _ready_notice: function(url){
        $("input[name='SalesOrder[approval_id]']").each(function(i,data){
            _flow_id = $(this).val();
            if(_flow_id != ''){
	            _id = $(this).attr("id");
	            _ck = +this.checked;
	            _box_id = "notice_box_" + _flow_id;
	            _box = "<label id='" + _box_id + "' class='red'></label>";
	            $("#"+_box_id).remove();
	            $("#" + _id + " + label").after(_box);
	            $.ajax({
	                type:"POST",
	                dataType:"json",
	                async: false,
	                url: url + "&flow_id=" + _flow_id,
	                success: function(json){
	                    if (!json.flag) {
	                        $("#notice_box_"+_flow_id).html(json.msg);
	                        $("#" + _id).attr("disabled", true).attr("checked",false);
	                    }
	                }
	            });
            }
        });
    },
}
