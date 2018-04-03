/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
require(['jquery', 'jquery/ui'],
    function ($) {
        $(document).ready(function(){ //use the jquery only after the complete document has loaded so jquery event gets bound to the html elements
        	$("#amconnectorsync_customersync_syncdirection").on('change',function(){
        	    alert('If you change sync direction then attribute mapping direction will also change according to sync direction');
        	});

        	$("#amconnectorsync_productsync_syncdirection").on('change',function(){
            	alert('If you change sync direction then attribute mapping direction will also change according to sync direction');
        	});   
			$("#amconnectorsync_categorysync_syncdirection").on('change',function(){
            	alert('If you change sync direction then attribute mapping direction will also change according to sync direction');
        	});
            $('.hiddensupportemail').parent().parent().hide();
            $('#show-email').parent().parent().hide();
        })
    });

function support(url,supportvalue)
{
    url = url+ '?supportType=' + supportvalue;
    jQuery.ajax({
        url: url,
        cache: false,
        success: function(html){
            if(html) {
                jQuery('#email').val(html);
                jQuery('#show-email').parent().parent().show();
                jQuery('#show-email').text(html).show();
                if(supportvalue == 6)
                {
                    alert("This should be used only for emergency requests");
                }
            }else{
                jQuery('#show-email').parent().parent().hide();
            }
        }
    });
}

function termsPopAction(baseurl)
{
  require([
      "jquery",
      "Kensium_Amconnector/js/jquery.colorbox-min"
  ], function (jQuery) {

      jQuery(document).ready(function () {
          jQuery.colorbox({
              html: "<div class='license-agreement'><h3>SOFTWARE LICENSE AGREEMENT</h3><p>Kensium Solutions, along with its subsidiaries, divisions, and affiliates (&quot;KENSIUM&quot;), provides this software for use by the CUSTOMER under the following terms and conditions.</p> <p><strong>Acumatica-Magento Connector software product</strong> accompanying this SLA, includes computer software and may include associated source code, media, printed materials, and &quot;on-line&quot; or electronic documentation (Together hereinafter called as &quot;<strong>SOFTWARE PRODUCT</strong>&quot;). By installing, copying, or otherwise using the SOFTWARE PRODUCT, CUSTOMER agrees to be bound by the terms of this SLA. If CUSTOMER does not agree to the terms of this SLA, Shall not install, use, distribute in any manner, or replicate in any manner, any part, file or portion of the SOFTWARE PRODUCT. </p><ul><li><strong>IPR AND ENFORCEMENT:</strong> The SOFTWARE PRODUCT is protected by copyright laws and international copyright treaties, as well as other intellectual property laws and treaties. The SOFTWARE PRODUCT is licensed, not sold. If the licensed right of use for this SOFTWARE PRODUCT is purchased by CUSTOMER with any intent to reverse engineer, decompile, create derivative works, and the exploitation or unauthorized transfer of, any intellectual property and trade secrets, to include any exposed methods or source code where provided, no licensed right of use shall exist, and any products created as a result shall be judged illegal by definition of all applicable law. Any sale or resale of intellectual property or created derivatives so obtained will be prosecuted to the fullest extent of all local, federal and international law.</li><li><strong>GRANT OF LICENSE:</strong> KENSIUM hereby grants to CUSTOMER for the term of this agreement, a non-exclusive, non-transferable licensee to execute, display, and perform and to use the current version of the SOFTWARE PRODUCT on its Servers owned or leased by and in full control of CUSTOMER.<ul><li>This SLA, if legally executed as defined herein, licenses and so grants <ul><li>SOFTWARE PRODUCT is deployed on the server of the CUSTOMER and allows users to access the same for their regular usage via a browser. </li><li>There is no deployment license fee at the time of installation for each new license. </li></ul></li></ul><li><strong>DESCRIPTION OF OTHER RIGHTS AND LIMITATIONS: </strong><ul><li><strong>Not for Resale Software</strong>. The SOFTWARE PRODUCT is provided as &quot;Not for Resale&quot; or &quot;NFR&quot;, notwithstanding other sections of this SLA, CUSTOMER may not resell, distribute, or otherwise transfer for value or benefit in any manner, the SOFTWARE PRODUCT. CUSTOMER may not transfer, rent, lease, lend, copy, modify, translate, sublicense, time-share or electronically transmit the SOFTWARE PRODUCT, media or documentation. This also applies to any and all intermediate files, source code, and compiled executables.</li><li><strong>Limitations on Reverse Engineering, De-compilation, and Disassembly.</strong> CUSTOMER may not reverse engineer, decompile, create derivative works, modify, translate, or disassemble the SOFTWARE PRODUCT, and only to the extent that such activity is expressly permitted by applicable law notwithstanding this limitation. CUSTOMER agrees to take all reasonable, legal and appropriate measures to prohibit the illegal dissemination of the SOFTWARE PRODUCT or any of its constituent parts and redistributables to the fullest extent of all applicable local legislations and International Laws and Treaties regarding anti-circumvention, including but not limited to, the Geneva and Berne World Intellectual Property Organization (WIPO) Diplomatic Conferences.</li><li><strong>Rental.</strong> CUSTOMER may not rent, lease, or lend the SOFTWARE PRODUCT.</li><li><strong>Separation of Components, Their Constituent Parts and Redistributables.</strong> The SOFTWARE PRODUCT is licensed as a single product. The SOFTWARE PRODUCT and its constituent parts and any provided redistributables may not be reverse engineered, decompiled, disassembled, nor placed for distribution, sale, or resale as individual creations by CUSTOMER or any individual not expressly given such permission by KENSIUM. The provision of source code, if included with the SOFTWARE PRODUCT, does not constitute transfer of any legal rights to such code, and resale or distribution of all or any portion of all source code and intellectual property. CUSTOMER will be prosecuted to the fullest extent of all applicable local, federal and international laws in cases of violation. All KENSIUM libraries, source code, redistributables and other files remain KENSIUM's exclusive property. CUSTOMER may not distribute any files, except those that KENSIUM has expressly designated as Redistributable. </li></ul></li><li><strong>SUPPORT SERVICES:</strong> KENSIUM may provide CUSTOMER with support services related to the SOFTWARE PRODUCT (&quot;Support Services&quot;) on subscription of the same at a price that is agreed from time to time. Use of Support Services is governed by KENSIUM policies and programs described in the user manual, in on-line documentation and/or other KENSIUM provided materials. With respect to technical information CUSTOMER provide to KENSIUM as part of the Support Services, KENSIUM may use such information for its business purposes, including for product support and development. </li><li><strong>SOFTWARE TRANSFER:</strong> CUSTOMER may NOT permanently or temporarily transfer ANY of rights under this SLA to any individual or entity. However if the CUSTOMER is acquired or merged with other entity, the entity thus acquired or the entity thus formed will replace the CUSTOMER automatically; the information about the merger or acquisition need to be provided by the CUSTOMER.  KENSIUM reserves the right to object the replacement and its decision cannot be challenged.</li><li><strong>TERMINATION:</strong> Without prejudice to any other rights or remedies, KENSIUM will terminate this SLA upon CUSTOMER failure to comply with all the terms and conditions of this SLA. In such event, CUSTOMER must destroy all copies of the SOFTWARE PRODUCT and all of its component parts including any related documentation, and must remove ANY and ALL use of such technology with the next generally available release from any applications using technology contained in the SOFTWARE PRODUCT, whether in native, altered or compiled state.</li><li><strong>UPGRADES.</strong> If the SOFTWARE PRODUCT is labeled as an upgrade, CUSTOMER must be properly licensed to use the SOFTWARE PRODUCT identified by KENSIUM as being eligible for the upgrade in order to use the SOFTWARE PRODUCT.  A SOFTWARE PRODUCT labeled as an upgrade replaces and/or supplements the SOFTWARE PRODUCT that formed the basis for CUSTOMER eligibility for the upgrade, and together constitute a single SOFTWARE PRODUCT. CUSTOMER may use the resulting upgraded SOFTWARE PRODUCT only in accordance with all the terms of this SLA.</li><li><strong>COPYRIGHT:</strong> All title and copyrights in and to the SOFTWARE PRODUCT (including but not limited to any images, demos, source code, intermediate files, packages, photographs, animations, video, audio, music, text, and &quot;applets&quot; incorporated into the SOFTWARE PRODUCT), the accompanying printed materials, and any copies of the SOFTWARE PRODUCT are owned by KENSIUM or its subsidiaries. The SOFTWARE PRODUCT is protected by copyright laws and international treaty provisions. Therefore, CUSTOMER must treat the SOFTWARE PRODUCT like any other copyrighted material except that CUSTOMER may install the SOFTWARE PRODUCT for use by CUSTOMER. CUSTOMER may not copy any printed materials accompanying the SOFTWARE PRODUCT.</li><li><strong>GENERAL PROVISIONS.</strong> This SLA may only be modified in writing signed by CUSTOMER and an authorized officer of KENSIUM. If any provision of this SLA is found void or unenforceable, the remainder will remain valid and enforceable according to its terms.</li><li><strong>MISCELLANEOUS.</strong> CUSTOMER agree that any local law(s) to the benefit and protection of KENSIUM ownership of, and interest in, its intellectual property and right of recovery for damages thereto will also apply. Should CUSTOMER have any questions concerning this SLA, or if CUSTOMER desire to contact KENSIUM for any reason, CUSTOMER CAN contact us via email to support@kensium.com.</li><li><strong>NO WARRANTIES:</strong>  KENSIUM EXPRESSLY DISCLAIMS ANY WARRANTY FOR THE SOFTWARE PRODUCT. THE PRODUCT AND ANY RELATED DOCUMENTATION IS PROVIDED &quot;AS IS&quot; WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING, WITHOUT LIMITATION, THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NONINFRINGEMENT. THE ENTIRE RISK ARISING OUT OF USE OR PERFORMANCE OF THE PRODUCT REMAINS WITH CUSTOMER.</li><li><strong>LIMITATION OF LIABILITY:</strong> TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, IN NO EVENT SHALL KENSIUM OR ITS SUPPLIERS BE LIABLE FOR ANY SPECIAL, INCIDENTAL, INDIRECT, OR CONSEQUENTIAL DAMAGES WHATSOEVER (INCLUDING, WITHOUT LIMITATION, DAMAGES FOR LOSS OF BUSINESS PROFITS, BUSINESS INTERRUPTION, LOSS OF BUSINESS INFORMATION, ANY OTHER PECUNIARY LOSS, ATTORNEY FEES AND COURT COSTS) ARISING OUT OF THE USE OF OR INABILITY TO USE THE SOFTWARE PRODUCT OR THE PROVISION OF OR FAILURE TO PROVIDE SUPPORT SERVICES, EVEN IF KENSIUM HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</li></ul></div>",
              rel: 'group3',
              innerWidth: "50%",
              innerHeight: "50%",
              title: "<div style  = 'float:left;width:100%;background-size:100% 100%;height:50px;background-image:url(" + baseurl + "adminhtml/Magento/backend/en_US/Kensium_Amconnector/images/header_bg.gif);' > <span style='height: 36px; width: 35px; padding: 2px; text-align: center; margin: 6px -5px 0px 10px; float: left; background:#fff url(" + baseurl + "adminhtml/Magento/backend/en_US/Kensium_Amconnector/images/amconnector/amconnector_medium.png) no-repeat 2px 2px; border-radius: 20px;' ></span>  <span style = 'float:left;font-size:20px;font-weight:bold;color:white;padding: 14px; ' >Acumatica Magento Connector 2.1</span></span> </div>",
          });
      });
  });

}

require([
    'jquery',
    'tinymce',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'loadingPopup',
    'mage/backend/floating-header'
], function (jQuery, tinyMCE, confirm) {
    'use strict';

    /**
    * Sync Category
    */
    function syncNow(url) {
        confirm({
            content: 'Are you sure you want to sync?',
            actions: {
                confirm: function () {
                    location.href = url;
                }
            }
        });
    }

    function displayLoadingMask() {
        jQuery('body').loadingPopup();
    }

    window.syncNow = syncNow;
    window.displayLoadingMask = displayLoadingMask;
});
