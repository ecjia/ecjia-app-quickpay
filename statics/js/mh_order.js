// JavaScript Document
;(function (app, $) {
    app.order_list = {
        init: function () {
        	//筛选功能
        	$(".screen-btn").on('click', function(e){
				e.preventDefault();
				var activity_type = $("select[name='activity_type']").val();
				var url = $("form[name='searchForm']").attr('action');
				if (activity_type != '') {
	                   url += '&activity_type=' + activity_type;
	            }
                ecjia.pjax(url);
			});
        	
        
            //搜索功能
            $("form[name='searchForm'] .btn-primary").on('click', function (e) {
                e.preventDefault();
                var url = $("form[name='searchForm']").attr('action');
                var keywords = $("input[name='keywords']").val();
                if (keywords != '') {
                    url += '&keywords=' + keywords;
                }
                ecjia.pjax(url);
            });
        }
    };
    
    app.order_info = {
	    init: function () {
	        var $form = $("form[name='theForm']");
	        var option = {
	            rules: {
	            	action_note: {
	                    required: true
	                }
	            },
	            messages: {
	            	action_note: {
	                	required: "请输入操作备注"
	                }
	            },
	            submitHandler: function () {
	                $form.ajaxSubmit({
	                    dataType: "json",
	                    success: function (data) {
	                        ecjia.merchant.showmessage(data);
	                    }
	                });
	            }
	        }
	        var options = $.extend(ecjia.merchant.defaultOptions.validate, option);
	        $form.validate(options);
	    
	    }
	};
})(ecjia.merchant, jQuery);
 
// end