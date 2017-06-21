// JavaScript Document
;(function (app, $) {
    app.quickpay_list = {
        init: function () {
        	//开启闪惠
        	$("#ajaxopen").on('click', function (e) {
        		e.preventDefault();
        		var url = $(this).attr('href');
        		$.get(url, function (data) {
        			ecjia.merchant.showmessage(data);
        		}, 'json');
        	});
        	
        	//关闭闪惠
            $('#ajaxclose').on('click', function() {
                var $this = $(this);
                var message = $this.attr('data-msg');
                var url = $this.attr('data-href');
                if (message != undefined) {
                      smoke.confirm(message, function(e) {
                            if (e) {
                                  $.get(url, function(data){
                                        ecjia.merchant.showmessage(data);
                                  })
                            }
                      }, {ok:"确定", cancel:"取消"});
                }
            });

	    	 
        	//筛选功能
			$('form[name="searchForm"]').on('submit', function(e){
				e.preventDefault();
				var ip = $("select[name='ip']").val();
				var userid = $("select[name='userid']").val();
				var keyword = $("input[name='keyword']").val();
				var url = $("form[name='searchForm']").attr('action');

				if(ip == 'undefind')ip='';
				if(userid == 'undefind')userid='';
				ecjia.pjax(url + '&ip=' + ip + '&user_id=' + userid + '&keyword=' + keyword);
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
 
    app.quickpay_info = {
        init: function () {
            /* 加载日期控件 */
			$.fn.datetimepicker.dates['zh'] = {  
                days:       ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六","星期日"],  
                daysShort:  ["日", "一", "二", "三", "四", "五", "六","日"],  
                daysMin:    ["日", "一", "二", "三", "四", "五", "六","日"],  
                months:     ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月","十二月"],  
                monthsShort:  ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月","十二月"], 
                meridiem:    ["上午", "下午"],  
                today:       "今天"  
	        };
			
            $(".date").datetimepicker({
                format: "yyyy-mm-dd hh:ii",
                language: 'zh',  
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                minuteStep: 1
            });
            
            app.quickpay_info.submit_form();
            
        },
    
    
	    submit_form: function (formobj) {
	        var $form = $("form[name='theForm']");
	        var option = {
	            rules: {
	            	title: {
	                    required: true
	                },
	            },
	            messages: {
	                title: {
	                	required: "请输入闪惠名称"
	                },
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