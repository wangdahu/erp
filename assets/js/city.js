jQuery(function($) {
    // 选择省份加载城市
    var city = $(".city_value"),
    url = "index.php?r=/pss/common/district";
    $(".province_blur").change(function(e, cityId) {
        var province = $(this), recall,
        pid = this.value,
        data = province.data(pid);
        if(data) { // 从缓存取，每个省只加载一次
            var html = [], isEmpty;
            data.forEach(function(item) {
                html.push('<option value="' + item[0] + '">' + item[1] + '</option>');
            });
            city.html(html.join(""));
            cityId && city.val(cityId);
        } else if(pid) {
            recall = arguments.callee;
            city.html('<option value="">loading...</option>');
            $.getJSON( url, {pid: this.value}, function(json){
                province.data(pid, json);
                recall.call(province[0], null, cityId);
            });
        } else {
            city[0].length = 1;
        }
    });

    // 显示国内或国外地址
    $(".country_blur").change(function() {
        var country = $(this),
        isChina = this.value == 1;
        $(".for-china").toggle(isChina);
    }).change();
});
