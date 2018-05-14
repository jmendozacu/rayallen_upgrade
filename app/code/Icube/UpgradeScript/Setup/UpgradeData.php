<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\UpgradeScript\Setup;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

    	

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /*Contact Us*/
		    $pageContent = <<<EOD
            <div class="col-md-5">
            {{block class="Magento\Cms\Block\Block" block_id="contact-info_rayallen"}}
            {{block class="Magento\Cms\Block\Block" block_id="contact-info_rayallenb2b"}}
{{block class="Magento\Cms\Block\Block" block_id="contact-info_gundog"}}
{{block class="Magento\Cms\Block\Block" block_id="contact-info_jjdog"}}
{{block class="Magento\Cms\Block\Block" block_id="contact-info_signaturek9"}}
    </div>
<div class="col-md-1">  </div>
<div class="col-md-6">
<p class="normaltext"><span style="color: #c63d43">*</span> Indicates a required field.</p>
{{widget type="Amasty\Customform\Block\Init" template="init.phtml" form_id="2"}}
</div>
EOD;

		    $cmsPage = $this->createPage()->load('contact-us', 'identifier');

		    if (!$cmsPage->getId()) {
		        $cmsPageContent = [
		            'title' => 'Contact Us',
		            'content_heading' => 'Contact Us',
		            'identifier' => 'contact-us',
		            'content' => $pageContent,
		            'is_active' => 1,
		            'stores' => 0,
		            'sort_order' => 0,
		        ];
		        $this->createPage()->setData($cmsPageContent)->save();
		    } else {
		        $cmsPage->setContent($pageContent)->save();
		    }

            /*Quote*/
            $pageContent = <<<EOD
            <p>Please fill out the following form.</p>
<p><span class="custom-required">*</span>Indicates a required field.</p>
{{widget type="Amasty\Customform\Block\Init" template="init.phtml" form_id="1"}}
<p class="quote-desc">If this online form will not accommodate your quote request, or if you prefer to use a written form, you may download our <br> <a href="https://www.rayallen.com/pub/media/RAM-quote-form-2014.pdf" target="_new" style="text-decoration: underline;">Request for Quote pdf form </a> and fax it to Ray Allen Manufacturing. Our contact information, and written instructions, are included on the form.</p>

<div id="quote-text" style="display:none">
<p class="quote-description">To add a product to your quote, please complete the fields below and click submit.<span style="color: #F00; font-weight: bold;">PLEASE NOTE: </span>If an Item# ends in "-P" then you will need to specify the color/size/style of this item in the Description field. Omitting this information may delay processing of your quote. </p>
</div>
EOD;

            $cmsPage = $this->createPage()->load('requestquote', 'identifier');

            if (!$cmsPage->getId()) {
                $cmsPageContent = [
                    'title' => 'Quotes',
                    'content_heading' => 'Request a Quote',
                    'identifier' => 'requestquote',
                    'content' => $pageContent,
                    'is_active' => 1,
                    'stores' => 0,
                    'sort_order' => 0,
                ];
                $this->createPage()->setData($cmsPageContent)->save();
            } else {
                $cmsPage->setContent($pageContent)->save();
            }

            /**
         * Contact Us Info (Rayallen)
         */

        $cmsBlockContent = <<<EOD
<p>We look forward to serving you! Please contact us using the form below or any of the other methods listed here. Just want to learn more, click <a href="https://www.rayallen.com/about-us" style="text-decoration: underline;">About Us</a>.</p>
<span itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">
        <p><strong>Ray Allen Manufacturing, LLC</strong><br><span itemprop="streetAddress">975 Ford Street</span><br>
       <span itemprop="addressLocality"> Colorado Springs </span>, 
        <span itemprop="addressRegion">CO </span>
        <span itemprop="postalCode">80915</span></p>

        <p><strong>Phone</strong><br>
            <span itemprop="telephone">(800) 444-0404</span>
         &nbsp; Toll-Free Order Desk<br>
            (719) 380-0404 &nbsp; Customer Service</p>

        <p><strong>Fax</strong><br>
            (719) 380-9730</p>

        <p><strong>Email</strong><br>
            <a href="mailto:sales@rayallen.com" class="body">sales@rayallen.com</a></p>

        <p><strong>Hours</strong><br>
            Monday - Friday<br>
            9am-6pm, Eastern Time</p>

        <p><strong>Website</strong><br>
            <a href="http://www.rayallen.com" class="body">www.rayallen.com</a></p></span>
EOD;
            $cmsBlock = $this->createBlock()->load('contact-info_rayallen', 'identifier');

            if (!$cmsBlock->getId()) {

                $cmsBlock = [
                    'title' => 'Contact Us Info (Rayallen)',
                    'identifier' => 'contact-info_rayallen',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 1,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }

            /**
         * Contact Us Info (Gondog)
         */

        $cmsBlockContent = <<<EOD
        <p>We look forward to serving you! Please contact us using the form below or any of the other methods listed here. Just want to learn more, click <a href="https://www.gundogdirect.com/about-gun-dog-direct" style="text-decoration: underline;">About Us</a>.</p>
<span itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">
        <p><strong>Gun Dog Direct</strong><br><span itemprop="streetAddress">975 Ford Street</span><br>
<span itemprop="addressLocality"> Colorado Springs </span>, 
        <span itemprop="addressRegion">CO </span>
        <span itemprop="postalCode">80915</span>

        </p><p><strong>Phone</strong><br>
            <span itemprop="telephone">(719) 380-0404</span></p>

        <p><strong>Email</strong><br>
            <a href="mailto:sales@gundogdirect.com" class="body">sales@gundogdirect.com</a></p>

        <p><strong>Hours</strong><br>
            Monday - Friday<br>
                    9am-6pm, Eastern Time</p>
</span>

EOD;
            $cmsBlock = $this->createBlock()->load('contact-info_gundog', 'identifier');

            if (!$cmsBlock->getId()) {

                $cmsBlock = [
                    'title' => 'Contact Us Info (Gondog)',
                    'identifier' => 'contact-info_gundog',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 4,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }

            /**
         * Contact Us Info (JJDog)
         */

        $cmsBlockContent = <<<EOD
        <p>If you have questions, concerns, suggestions or comments, we would like to hear from you. You may provide hard-copy communication by mail or FAX to:</p>
        <p>Gift Certificates can not be used online, please call, fax, or mail your order in.</p>
        <span itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address"><p><strong>Postal Address:</strong><br>J&amp;J Dog Supplies<span itemprop="streetAddress">975 Ford St.</span></p>

        <p><span itemprop="addressLocality">Colorado Springs </span>, 
        <span itemprop="addressRegion"> CO</span>
        <span itemprop="postalCode">80915</span><br>
            U.S.A.<br>
            Fax: 1 (719) 380-9730 (24-hours)</p>

        <p><a href="mailto:sales@jjdog.com" class="body">sales@jjdog.com</a></p>

                <p>Or phone us Monday-Friday 9am - 6pm, Eastern Time<br>
    ET:         <span itemprop="telephone">(800) 642-2050 or (719) 434-5980</span>
            Or send us e-mail at: <a href="mailto:sales@jjdog.com" class="body">sales@jjdog.com</a><br></p>
           

        <p>To report technical problems with our web site, e-mail:<a href="mailto:sales@jjdog.com" class="body">sales@jjdog.com</a><br></p>
   
   </span>

EOD;
            $cmsBlock = $this->createBlock()->load('contact-info_jjdog', 'identifier');

            if (!$cmsBlock->getId()) {

                $cmsBlock = [
                    'title' => 'Contact Us Info (JJDog)',
                    'identifier' => 'contact-info_jjdog',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 2,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }

            /**
         * Contact Us Info (Signature K9)
         */

        $cmsBlockContent = <<<EOD
        <p>We look forward to serving you! Please contact us using the form below or any of the other methods listed here.  If you just want to learn more, click <a href="https://www.signaturek9.com/signaturek9-about-us" style="text-decoration: underline;">About Us</a>.<br><b> NOTE: </b> Ray Allen Manufacturing is the parent company of Signature K9.</p>

        <p><strong>Ray Allen Manufacturing, LLC</strong><br>975 Ford Street<br>Colorado Springs, CO 80915</p>

        <p><strong>Phone</strong><br>
            (800) 444-0404 &nbsp; Toll-Free Order Desk<br>
            (719) 380-0404 &nbsp; Customer Service</p>

        <p><strong>Fax</strong><br>
            (719) 380-9730</p>

        <p><strong>Email</strong><br>
            <a href="mailto:sales@signaturek9.com" class="body">sales@signaturek9.com</a></p>

        <p><strong>Hours</strong><br>
            Monday - Friday<br>
            9am-6pm, Eastern Time</p>

        <p><strong>Website</strong><br>
            <a href="http://www.signaturek9.com" class="body">www.signaturek9.com</a></p>

EOD;
            $cmsBlock = $this->createBlock()->load('contact-info_signaturek9', 'identifier');

            if (!$cmsBlock->getId()) {

                $cmsBlock = [
                    'title' => 'Contact Us Info (Signature K9)',
                    'identifier' => 'contact-info_signaturek9',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 3,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }

            /**
         * Contact Us Info (Rayallen B2B)
         */

        $cmsBlockContent = <<<EOD
<p>We look forward to serving you! Please contact us using the form below or any of the other methods listed here. Just want to learn more, click <a href="https://stg.rayallenb2b.com/about-us" style="text-decoration: underline;">About Us</a>.</p>
<span itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">
        <p><strong>Ray Allen Manufacturing, LLC</strong><br><span itemprop="streetAddress">975 Ford Street</span><br>
       <span itemprop="addressLocality"> Colorado Springs </span>, 
        <span itemprop="addressRegion">CO </span>
        <span itemprop="postalCode">80915</span></p>

        <p><strong>Phone</strong><br>
            <span itemprop="telephone">1-800-444-0404 ext 148</span>
         &nbsp; Toll-Free Order Desk<br>
            (719) 380-0404 &nbsp; Customer Service</p>

        <p><strong>Fax</strong><br>
            (719) 380-9730</p>

        <p><strong>Email</strong><br>
            <a href="mailto:b2b@rayallen.com" class="body">b2b@rayallen.com</a></p>

        <p><strong>Hours</strong><br>
            Monday - Friday<br>
            9am-6pm, Eastern Time</p>

        <p><strong>Website</strong><br>
            <a href="http://www.rayallenb2b.com" class="body">www.rayallenb2b.com</a></p></span>
EOD;
            $cmsBlock = $this->createBlock()->load('contact-info_rayallenb2b', 'identifier');

            if (!$cmsBlock->getId()) {

                $cmsBlock = [
                    'title' => 'Contact Us Info (Rayallen B2B)',
                    'identifier' => 'contact-info_rayallenb2b',
                    'content' => $cmsBlockContent,
                    'is_active' => 1,
                    'stores' => 5,
                ];
                $this->createBlock()->setData($cmsBlock)->save();
            } else {
                $cmsBlock->setContent($cmsBlockContent)->save();
            }
        }
    }
    /**
     * Create page
     *
     * @return Page
     */
    public function createPage()
    {
        return $this->pageFactory->create();
    }
    /**
     * Create block
     *
     * @return Page
     */
    public function createBlock()
    {
        return $this->blockFactory->create();
    }
}