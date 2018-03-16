<?php
class DataFeed
    extends \Magento\Framework\App\Http
    implements \Magento\Framework\AppInterface {
    public function launch()
    {

$products = $this->_objectManager->create('\Magento\Catalog\Model\Product')
->getCollection()
->addStoreFilter(2)
->addAttributeToSelect('sku')
->addAttributeToSelect('name')
->addAttributeToSelect('description')
->addAttributeToSelect('product_type')
->addAttributeToSelect('url_key')
->addAttributeToSelect('type_id')
->addAttributeToSelect('price')
->addAttributeToSelect('special_price')
->addAttributeToSelect('image')
->addAttributeToSelect('special_from_date')
->addAttributeToSelect('color')
->addAttributeToSelect('size')
->addAttributeToSelect('category_ids')
->addAttributeToSelect('status', 1)
->addAttributeToFilter('visibility',4);
$products = $products->addFieldToFilter('status',1);

echo 'Count '.count($products);
echo "\n";
$fp = fopen(__DIR__ .'/googlefeed/jjdog.txt', 'w');
$i=0;
fputcsv($fp,array('gtin', 'mpn', 'id', 'title', 'description', 'product_type', 'link', 'image_link', 'price', 'color', 'size', 'availability', 'condition', 'gender', 'brand', 'google_product_category', 'shipping_weight', 'shipping', 'tax', 'adwords_grouping', 'adwords_labels', 'product_review_average', 'product_review_count', 'excluded_destination', 'material', 'item_group_id', 'pattern', 'age_group', 'sale_price_effective_date', 'sale_price','adwords_redirect', 'identifier_exists', 'multipack', 'adult', 'promotion_id', 'custom_label_0', 'custom_label_1', 'custom_label_2', 'custom_label_3', 'custom_label_4', 'mobile_link', 'size_type', 'size_system', 'is_bundle', 'availability_date','additional_image_link'),"\t");
$allProductSku = array();
foreach($products as $product)
{  
//	if($i==9)
//	break; 
//echo '<pre>';
//print_r($product->getData());
$color=$product->getData('color');
$size=$product->getData('size');
$_product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($product->getId());
$allProductSku[] = $product->getSku();
$associateproducts = array();
if ($_product->getTypeId() == 'configurable') {
       $_product = $this->_objectManager->get('Magento\Catalog\Model\Product')->loadByAttribute('sku',$product->getSku()); 
        $productType = $_product->getTypeInstance();
        $associateproducts = $productType->getUsedProducts($_product);
		$childPrice = array();
            foreach ($associateproducts as $childProduct) {
                $childPrice[] = $childProduct->getData('price');                
            } 
		if($childPrice) {
		    $price = min($childPrice);
		}
} else {
	$price = $product->getData('price');
}
if($_product->getAttributeSetId() == '32') {
    $waist = $product->getData('waist');
    $attr = $_product->getResource()->getAttribute('waist');
    if ($attr->usesSource()) {
       $size = html_entity_decode($attr->getSource()->getOptionText($waist));
    }
} else {
if($size) {
$attr = $_product->getResource()->getAttribute('size');
 if ($attr->usesSource()) {
       $size = html_entity_decode($attr->getSource()->getOptionText($size));
 }
}
}
if($color) {
$colorattr = $_product->getResource()->getAttribute('color');
 if ($colorattr->usesSource()) {
       $color = html_entity_decode($colorattr->getSource()->getOptionText($color));
 }
}
//echo "\n".$product->getData('sku');
$catids=$product->getData('category_ids');
$catName='';
$j = 0;
$cateNames = array();
foreach($catids as $catid){
    $cname = $this->_objectManager->create('\Magento\Catalog\Model\Category')->load($catid)->getName();
    if(in_array(trim($cname),$cateNames)) {

    } else if(trim($cname) != 'Default Category'){
        $cateNames[] = 	trim($cname);
	    if($j != 0)
		    $catName.='>';

	    $catName.=trim($cname);	
	    $j++;
	}
}

/** collect product brand and google_product_category data from custom table **/
$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Magento\Framework\App\ResourceConnection');
$connection= $this->_resources->getConnection();
$prodSku = '';
if(substr($product->getSku(), -2) == '-P') {
	$prodSku = substr($product->getSku(), 0, -2);
} else {
	$prodSku = $product->getSku();
}
/*$select = $connection->select()
    ->from(
        ['o' =>  $this->_resources->getTableName('data_feed_product_attr_data')]
    )->where('mpn=?',$prodSku);

$result = $connection->fetchrow($select);
*/
$brand = $gpcategory = $gtin = '';
//if(empty($product->getData('brand')) || empty($product->getData('google_product_category'))) {
    $select = $connection->select()
        ->from(
            ['o' =>  $this->_resources->getTableName('data_feed_product_attr_data')]
        )->where('mpn=?',$prodSku);

    $result = $connection->fetchrow($select);

    if(empty($_product->getData('brand'))) {
        $brand = $result['brand'];
    } else {
        $brand = $_product->getData('brand');
    }
    if(empty($_product->getData('google_product_category'))) {
        $gpcategory = $result['google_product_category'];
    } else {
        $gpcategory = $_product->getData('google_product_category');
    }
    if(empty($_product->getData('gtin'))) {
        $gtin = $result['gtin'];
    } else {
        $gtin = $_product->getData('gtin');
    }
//}

if($cateNames) {
    $cateFNames = $cateNames[0];
} else {
    $cateFNames= '';
}
//if(!empty($result['brand']) && !empty($result['google_product_category'])) {
if ($_product->getTypeId() != 'configurable') {
	fputcsv($fp, array(/*$result['gtin']*/ $gtin,$product->getSku(),$product->getSku(),$product->getName(),strip_tags($product->getDescription()),$catName,'https://www.jjdog.com/'.$product->getData('url_key'),'https://www.jjdog.com/pub/media/catalog/product'.$product->getData('image'),round($price,2).' USD',$color,$size,'in stock','New','unisex',$brand /*$result['brand'], $product->getBrand()*/,$gpcategory /*$result['google_product_category'], $product->getGoogleProductCategory()*/,'','','','','','','','','','','','Adult','','','','','','','',$cateFNames,'','','','','','','','','',''),"\t");
}
foreach ($associateproducts as $childProduct) {
	if($childProduct->getStatus() != 1) { continue;}
	if(in_array($childProduct->getSku(), $allProductSku)) {
	} else {
    		$allProductSku[] = $childProduct->getSku();

			$childProductcatids=$product->getData('category_ids');
			$childProductcatName='';
			$childProductsize = '';
			$childProductColor = '';
			$j = 0;
			$childProductcateNames = array();
			foreach($childProductcatids as $childProductcatid){
			    $childProductcname = $this->_objectManager->create('\Magento\Catalog\Model\Category')->load($childProductcatid)->getName();
                	    if(in_array(trim($childProductcname),$childProductcateNames)) {

                	    } else if(trim($childProductcname) != 'Default Category'){
                    		$childProductcateNames[] = 	trim($childProductcname);
	                	if($j != 0)
		                    $childProductcatName.='>';

	                	$childProductcatName.=trim($childProductcname);	
	                	$j++;
	            	    }
			}

			$childProductsize = $childProduct->getData('size');
if($_product->getAttributeSetId() == '32') {
    $waist = $childProduct->getData('waist');
    $attr = $childProduct->getResource()->getAttribute('waist');
    if ($attr->usesSource()) {
       $childProductsize = html_entity_decode($attr->getSource()->getOptionText($waist));
    }
} else {
	if($childProductsize) {
			$childProductattr = $childProduct->getResource()->getAttribute('size');
			if ($childProductattr->usesSource()) {
				$childProductsize = html_entity_decode($childProductattr->getSource()->getOptionText($childProductsize));
			}
	}
}
			$childProductColor = $childProduct->getData('color');
if($childProductColor) {
			$childProductColorattr = $childProduct->getResource()->getAttribute('color');
			if ($childProductColorattr->usesSource()) {
				$childProductColor = html_entity_decode($childProductColorattr->getSource()->getOptionText($childProductColor));
			}
}
/*$childSelect = $connection->select()
    ->from(
        ['o' =>  $this->_resources->getTableName('data_feed_product_attr_data')]
    )->where('mpn=?',$childProduct->getSku());

$ChildResult = $connection->fetchrow($childSelect);
*/
$cbrand = $cgpcategory = $childProductGtin = '';	
//if(empty($childProduct->getBrand()) || empty($childProduct->getGoogleProductCategory())) {
    $childSelect = $connection->select()
        ->from(
            ['o' =>  $this->_resources->getTableName('data_feed_product_attr_data')]
        )->where('mpn=?',$childProduct->getSku());

    $ChildResult = $connection->fetchrow($childSelect);

    if(empty($childProduct->getData('brand'))) {
        $cbrand = $ChildResult['brand'];
    } else {
        $cbrand = $childProduct->getData('brand');
    }
    if(empty($childProduct->getData('google_product_category'))) {
        $cgpcategory = $ChildResult['google_product_category'];
    } else {
        $cgpcategory = $childProduct->getData('google_product_category');
    }
    if(empty($childProduct->getData('gtin'))) {
        $childProductGtin = $ChildResult['gtin'];
    } else {
        $childProductGtin = $childProduct->getData('gtin');
    }
//}
if($childProductcateNames) {
    $childProductcateFNames = $childProductcateNames[0];
} else {
    $childProductcateFNames= '';
}

            fputcsv($fp, array(/*$ChildResult['gtin']*/$childProductGtin,$childProduct->getSku(),$childProduct->getSku(),$childProduct->getName(),strip_tags($childProduct->getDescription()),$childProductcatName,'https://www.jjdog.com/'.$product->getData('url_key'),'https://www.jjdog.com/pub/media/catalog/product'.$product->getData('image'),round($childProduct->getData('price'),2).' USD',$childProductColor,$childProductsize,'in stock','New','unisex',$cbrand/*$ChildResult['brand'], $childProduct->getBrand()*/,$cgpcategory/*$ChildResult['google_product_category'], $childProduct->getGoogleProductCategory()*/,'','','','','','','','','',$product->getSku(), '','Adult','','','','','','','',$childProductcateFNames,'','','','','','','','','',''),"\t");        
        }

}

$i++;
}
 fclose($fp);
echo 'completed';
        //the method must end with this line
        return $this->_response;
    }

    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }

}
