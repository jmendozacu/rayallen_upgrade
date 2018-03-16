/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
function syncnow(url){
    jQuery(function(){
        jQuery('.admin__menu-overlay').show();
        if(!jQuery('#syncnow-mask').html()){
             jQuery('.admin__menu-overlay').after('<div class="admin__data-grid-loading-mask" id="syncnow-mask" data-component="sales_order_view_invoice_grid.sales_order_view_invoice_grid.sales_order_invoice_columns" data-role="spinner" style="display: block;"><div class="spinner"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div></div>');
        }
    });
    if(window.ActiveXObject){
        obj = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        obj = new XMLHttpRequest();
    }
    var today = new Date();
    var time = today.getMilliseconds();
    obj.open('GET',url);
    obj.onreadystatechange = function(){
        if(obj.readyState <4){
           // document.getElementById("loading-mask").style.display = 'block';
        }else if (obj.readyState ==4 /*&& obj.status ==200*/){
            var result  = obj.responseText;
            if(result) {
                alert(result);
                location.reload();
            } else {               
                location.reload();
            }

        }
    }
    obj.send(null);
}
