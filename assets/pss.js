$(function(){
	
	$.fn.openView = function(viewUrl){
		$(this).removeClass("arrow-open").addClass("arrow-close");
		$(this).parent().attr("style", "position:relative;background-color:#ffc600")
		.parent().after(function(){
			var colspan = $(this).children().length;
			return '<tr><td style="border:solid medium #ffc600;" colspan="'+colspan+'">'
			+ $.ajax({
				async:false,
				url: viewUrl,
				type:"GET",
			}).responseText
			+'</td></tr>';
		});
	};
	$.fn.closeView = function(){
		$(this).removeClass("arrow-close").addClass("arrow-open");
		$(this).parent().attr("style", "position:relative;")
		.parent().next().remove();
	};
});
