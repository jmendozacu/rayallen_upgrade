<?php
namespace Iglobal\Stores\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
     * Path for config.
     */
    const XML_CONFIG_PATH = 'iglobal_integration/';

    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'iGlobal/jquery/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        'jquery.js',
        'jquery.noconflict.js',
    );

    /**
     * @var \Magento\Framework\Url\Helper\Data
    */
    protected $_urlHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
    */
    protected $_imageHelper;

    /**
     * @var \Magento\Customer\Model\Session
    */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Address
    */
    protected $_address;

    public function __construct(
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Address $address

    ) {
        parent::__construct(
            $context
        );
        $this->_urlHelper = $urlHelper;
        $this->_customerSession = $customerSession;
        $this->_imageHelper = $imageHelper;
        $this->_address = $address;
    }


    public function units2lbs($value, $unit='lbs')
    {
        $unit = ($unit ? $unit : "lbs");
        $convert = array('kg' => 0.453592, 'oz' => 16, 'g' => 453.592, 'lbs' => 1);
        $value = round(floatval($value) / $convert[$unit], 2);
        if($value){
            return $value;
        }
        return null;
    }

    public function dim2inch($value, $dim='in')
    {
        $dim = ($dim ? $dim : "in");
        $convert = array('cm' => 2.54, 'in' => 1);
        $value = ceil(floatval($value) / $convert[$dim]);
        if($value){
            return $value;
        }
        return null;
    }

    public function getDimensions($product, $item)
    {
        $dim = $product->getAttributeText('ig_dimension_units');
        $weight = $product->getIgWeight();
        if (empty($weight))
        {
            $weight = $item->getWeight();
        }
        $dimensions = array(
            'weight' => $this->units2lbs($weight, $product->getAttributeText('ig_weight_units')),
            'length' => $this->dim2inch($product->getIgLength(), $dim),
            'width'  => $this->dim2inch($product->getIgWidth(), $dim),
            'height' => $this->dim2inch($product->getIgHeight(), $dim),
        );
        return $dimensions;
    }

    public function getItemDetails($item)
    {
        $product = $item->getProduct();
        // reload the product to get all attributes
        $product = $product->load($product->getId());
        //get options on the items
        $options = $product->getTypeInstance(true)->getOrderOptions($product);
        $optionList = "";
        if ($options && isset($options["attributes_info"])) {
            $optionList = "|";
            foreach ($options["attributes_info"] as $option) { //todo: add loops for $options[additional_options] and $options[attributes_info] to get all possible options
                $optionList = $optionList . $option["label"] . ':"' . $option["value"] . '"|';
            }
        }

        if($opt = $item->getOptionByCode('simple_product')) {
            $simpleProduct = $opt->getProduct();
            $simpleProduct = $simpleProduct->load($simpleProduct->getId());
        } else {
            $simpleProduct = $product;
        }
        if($simpleProduct->getImage()) {
            $imageUrl = $simpleProduct->getMediaConfig()->getMediaUrl($simpleProduct->getImage());
        } else {
            $imageUrl = $product->getMediaConfig()->getMediaUrl($product->getImage());
        }

      $details = array(
            'productId'   => $simpleProduct->getId(),
            'sku'         => $simpleProduct->getSku(),
            'description' => $product->getName(),
            'unitPrice'   => $item->getPrice(),
            'quantity'    => $item->getQty(),
            'itemURL' => $product->getProductUrl(),
            'imageURL' => str_replace("http:", "https:", $imageUrl),
            'itemDescriptionLong' => $optionList,
            'countryOfOrigin' => $this->getProductAttribute($product, 'country_of_origin'),
            'itemBrand' => $this->getProductAttribute($product, 'brand'),
            'itemCategory' => $this->getProductAttribute($product, 'category'),
            'itemHSCode' => $this->getProductAttribute($product, 'hs_code'),
            'itemCustomization' => $this->getProductAttribute($product, 'customization'),
            'itemColor' => $this->getProductAttribute($product, 'color'),
            'itemMaterial' => $this->getProductAttribute($product, 'material'),
            'status' => $this->getProductAttribute($product, 'status'),
            'nonShippable' => (bool)$this->getProductAttribute($product, 'non_shippable')
        ) + $this->getDimensions($product, $item);
        return $details;
    }

    /**
     * Check enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->_getConfigValue('igjq/enabled');
    }

    /**
     * Check if the country is domestic
     * @return bool
     */
    public function isDomestic(){
        $countryCode = (isset($_COOKIE['igCountry']) ? $_COOKIE['igCountry'] : "US");  // Default to US.  This resolves issues with domestic customers going to int'l checkout if welcome mat fails
        $domesticCountries = explode(",", $this->scopeConfig->getValue('general/country/ig_domestic_countries', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        return in_array($countryCode, $domesticCountries);
    }

    /**
     * Create the iGlobal checkout url
     * @param $tempCart
     * @return string
     */
    public function getCheckoutUrl($tempCart){
        $subdomain = ($this->_getConfigValue('apireqs/igsubdomain') ? $this->_getConfigValue('apireqs/igsubdomain') : "checkout");
		$storeNumber = ($this->_getConfigValue('apireqs/iglobalid') ? $this->_getConfigValue('apireqs/iglobalid') : "3");
		$countryCode = (isset($_COOKIE['igCountry']) ? $_COOKIE['igCountry'] : "");
        $url = 'https://' . $subdomain . '.iglobalstores.com/?store=' . $storeNumber . '&tempCartUUID=' . $tempCart . '&country=' . $countryCode;


		//grab customer info if logged in and carry over to iglobal checkout
		if($this->_customerSession->isLoggedIn()){
            $customer = $this->_customerSession->getCustomer();
            $shipping_address = $this->_address->load($customer->getDefaultShipping());
            $customer_params = '';

            if(!$shipping_address){
                $customer_params .= '&customerName=' . $customer['firstname'] . ' ' . $customer['lastname'];
                $customer_params .= '&customerCompany=' . $customer['company'];
                $customer_params .= '&customerEmail=' . $customer['email'];
                $customer_params .= '&customerPhone=' . $customer['telephone'];
            }else{
                $customer_params .= '&customerName=' . $customer['firstname'] . ' ' . $customer['lastname'];
                $customer_params .= '&customerCompany=' . $customer['company'];
                $customer_params .= '&customerEmail=' . $customer['email'];
                $customer_params .= '&customerPhone=' . $customer['telephone'];
                $customer_params .= '&customerAddress1=' . $shipping_address['street'];
                $customer_params .= '&customerAddress2=' . $shipping_address['street_2'];
                $customer_params .= '&customerCity=' . $shipping_address['city'];
                $customer_params .= '&customerState=' . $shipping_address['region'];
                $customer_params .= '&customerCountry=' . $shipping_address['country_id'];
                $customer_params .= '&customerZip=' . $shipping_address['postcode'];
            }
            $url .= $customer_params;
        }
        return $url;
    }

    /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . $file;
    }

    /**
     * Create a json string of the confg settings for the welcome mat.
     * @return string
     */
    public function getJavascriptVars()
    {
        $data = array (
            'storeId' =>$this->_getConfigValue('apireqs/iglobalid'),
            'subdomain' => $this->_getConfigValue('apireqs/igsubdomain'),
            'flag_parent' => $this->_getConfigValue('igmat/flag_parent'),
            'flag_method' => $this->_getConfigValue('igmat/flag_method'),
            'flag_code' => $this->_getConfigValue('igmat/flag_code'),
            'domesticCountries' => $this->scopeConfig->getValue('general/country/ig_domestic_countries', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'storeLogo' => $this->_getConfigValue('igmat/store_logo'),
            'cartUrl' => $this->_urlHelper->_getUrl('checkout/cart'),
            'checkoutUrl' => $this->_urlHelper->_getUrl('iglobal/checkout'),
            'checkoutButtons' => $this->_getConfigValue('igmat/checkout_selectors'),
            'toggleElements' => $this->_getConfigValue('igmat/toggle_selectors')
        );
		//figure out what countries are serviced
		if ($this->scopeConfig->getValue('general/country/allow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			//define iGlobal serviced countries
			$allCountries = array("AP"=>"APO/FPO","AF"=>"Afghanistan","AL"=>"Albania","DZ"=>"Algeria","AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla","AG"=>"Antigua","AR"=>"Argentina","AM"=>"Armenia","AW"=>"Aruba","AU"=>"Australia","AT"=>"Austria","AZ"=>"Azerbaijan","BS"=>"Bahamas","BH"=>"Bahrain","BD"=>"Bangladesh","BB"=>"Barbados","BY"=>"Belarus","BE"=>"Belgium","BZ"=>"Belize","BJ"=>"Benin","BM"=>"Bermuda","BT"=>"Bhutan","BO"=>"Bolivia","BQ"=>"Bonaire","BA"=>"Bosnia & Herzegovina","BW"=>"Botswana","BR"=>"Brazil","VG"=>"Virgin Islands (British)","BN"=>"Brunei","BG"=>"Bulgaria","BF"=>"Burkina Faso","BI"=>"Burundi","KH"=>"Cambodia","CM"=>"Cameroon","CA"=>"Canada","IC"=>"Canary Islands","CV"=>"Cape Verde","KY"=>"Cayman Islands","CF"=>"Central African Republic","TD"=>"Chad","CL"=>"Chile","CN"=>"China - People's Republic of","CO"=>"Colombia","KM"=>"Comoros","CG"=>"Congo","CK"=>"Cook Islands","CR"=>"Costa Rica","HR"=>"Croatia","CW"=>"Curaçao","CY"=>"Cyprus","CZ"=>"Czech Republic","DK"=>"Denmark","DJ"=>"Djibouti","DM"=>"Dominica","DO"=>"Dominican Republic","TL"=>"Timor-Leste","EC"=>"Ecuador","EG"=>"Egypt","SV"=>"El Salvador","GQ"=>"Equatorial Guinea","ER"=>"Eritrea","EE"=>"Estonia","ET"=>"Ethiopia","FK"=>"Falkland Islands","FO"=>"Faroe Islands (Denmark)","FJ"=>"Fiji","FI"=>"Finland","FR"=>"France","GF"=>"French Guiana","GA"=>"Gabon","GM"=>"Gambia","GE"=>"Georgia","DE"=>"Germany","GI"=>"Gibraltar","GR"=>"Greece","GL"=>"Greenland (Denmark)","GD"=>"Grenada","GP"=>"Guadeloupe","GU"=>"Guam","GH"=>"Ghana","GT"=>"Guatemala","GG"=>"Guernsey","GN"=>"Guinea","GW"=>"Guinea-Bissau","GY"=>"Guyana","HT"=>"Haiti","HN"=>"Honduras","HK"=>"Hong Kong","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia","IQ"=>"Iraq","IE"=>"Ireland - Republic Of","IL"=>"Israel","IT"=>"Italy","CI"=>"Ivory Coast","JM"=>"Jamaica","JP"=>"Japan","JE"=>"Jersey","JO"=>"Jordan","KZ"=>"Kazakhstan","KE"=>"Kenya","KI"=>"Kiribati","KR"=>"Korea","KW"=>"Kuwait","KG"=>"Kyrgyzstan","LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LS"=>"Lesotho","LR"=>"Liberia","LT"=>"Lithuania","LI"=>"Liechtenstein","LU"=>"Luxembourg","MO"=>"Macau","MK"=>"Macedonia","MG"=>"Madagascar","MW"=>"Malawi","MY"=>"Malaysia","MV"=>"Maldives","ML"=>"Mali","MT"=>"Malta","MH"=>"Marshall Islands","MQ"=>"Martinique","MR"=>"Mauritania","MU"=>"Mauritius","YT"=>"Mayotte","MX"=>"Mexico","MD"=>"Moldova","FM"=>"Micronesia - Federated States of","MC"=>"Monaco","MN"=>"Mongolia","ME"=>"Montenegro","MS"=>"Montserrat","MA"=>"Morocco","MZ"=>"Mozambique","MM"=>"Myanmar","NA"=>"Namibia","NR"=>"Nauru","NP"=>"Nepal","NL"=>"Netherlands (Holland)","NV"=>"Nevis","NC"=>"New Caledonia","NZ"=>"New Zealand","NI"=>"Nicaragua","NE"=>"Niger","NG"=>"Nigeria","NU"=>"Niue Island","NF"=>"Norfolk Island","MP"=>"Northern Mariana Islands","NO"=>"Norway","OM"=>"Oman","PK"=>"Pakistan","PA"=>"Panama","PG"=>"Papua New Guinea","PY"=>"Paraguay","PW"=>"Palau","PE"=>"Peru","PH"=>"Philippines","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico","QA"=>"Qatar","RE"=>"Reunion","RO"=>"Romania","RU"=>"Russia","RW"=>"Rwanda","SM"=>"San Marino","ST"=>"Sao Tome & Principe","SA"=>"Saudi Arabia","SN"=>"Senegal","RS"=>"Serbia & Montenegro","SC"=>"Seychelles","SL"=>"Sierra Leone","SG"=>"Singapore","SK"=>"Slovakia","SI"=>"Slovenia","SB"=>"Solomon Islands","ZA"=>"South Africa","SS"=>"South Sudan","ES"=>"Spain","LK"=>"Sri Lanka","BL"=>"St. Barthelemy","EU"=>"St. Eustatius","KN"=>"St. Kitts and Nevis","LC"=>"St. Lucia","MF"=>"St. Maarten","VC"=>"St. Vincent","SD"=>"Sudan","SR"=>"Suriname","SZ"=>"Swaziland","SE"=>"Sweden","CH"=>"Switzerland","PF"=>"Tahiti","TW"=>"Taiwan","TJ"=>"Tajikistan","TZ"=>"Tanzania","TH"=>"Thailand","TG"=>"Togo","TO"=>"Tonga","TT"=>"Trinidad and Tobago","TN"=>"Tunisia","TR"=>"Turkey","TM"=>"Turkmenistan","TC"=>"Turks and Caicos Islands","TV"=>"Tuvalu","VI"=>"Virgin Islands (U.S.)","UG"=>"Uganda","UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","US"=>"United States","UY"=>"Uruguay","UZ"=>"Uzbekistan","VU"=>"Vanuatu","VE"=>"Venezuela","VN"=>"Vietnam","VA"=>"Vatican City","WS"=>"Western Samoa","YE"=>"Yemen","ZM"=>"Zambia","ZW"=>"Zimbabwe");

			//allowed country list
			$allowedCountries = $this->scopeConfig->getValue('general/country/allow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$allowedCountries = array_flip(explode("," , $allowedCountries));

			$excludedCountries = array_diff_key($allCountries, $allowedCountries);
			$data['noShipCountries'] = array_keys($excludedCountries);
		}
		return json_encode($data);
    }

    /**
     * Return list files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

    protected function _getConfigValue($key)
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH . $key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    protected  function getProductAttribute($product, $attributeName) {
        $value = $this->_getConfigValue('ig_item_attribute/' . $attributeName);
        if($value) {
            return $product->getData($value);
        }
        return null;
    }

}
