// JavaScript Document
; (function (app, $) {
    app.merchant_qrcode = {
        init: function() {
            app.merchant_qrcode.download_qrcode();
        },
        download_qrcode: function() {
        	
        	$('.download_qrcode').off('click').on('click', function(e) {
        		e.preventDefault();
        		var $this = $(this),
        			url = $this.attr('href');
        		
        		$.post(url, function(data) {
        			ecjia.merchant.showmessage(data);
        		});
        		
        	});
        },
        
    };
})(ecjia.merchant, jQuery);

//end