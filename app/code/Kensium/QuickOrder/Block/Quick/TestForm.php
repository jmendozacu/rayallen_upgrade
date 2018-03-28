<?php
/**
 * Copyright © 2015 Kensium . All rights reserved.
 */
namespace Kensium\QuickOrder\Block\Quick;
use Kensium\QuickOrder\Block\BaseBlock;
class TestForm extends BaseBlock
{
    
    protected $resProduct;
    protected $productCollection;
    protected $cart;
    protected $product;
    protected $saveHandler;
    protected $sessionGeneric;
    
            function __construct(\Kensium\QuickOrder\Block\Context $context,
                                 \Magento\Catalog\Model\ResourceModel\Product $resProduct,
                                 \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
                                 \Magento\Checkout\Model\Cart $cart,
                                 \Magento\Catalog\Model\Product $product,
                                 \Magento\Framework\Session\Generic $sessionGeneric
            ) {
                
                $this->resProduct = $resProduct;
                $this->productCollection = $productCollection;
                $this->cart = $cart;
                $this->product = $product;
                $this->sessionGeneric = $sessionGeneric;
                
                
        parent::__construct($context);
    }
    
    public function addSKUProductToCart()
    {
     //  die(print_r($_REQUEST));
        ini_set('display_errors','on');
        for($i=0;$i<count($_REQUEST['sku']);$i++ )
        {
            
        if ($_REQUEST['sku'][$i] != '')
        {
            $productId = $this->resProduct->getIdBySku($_REQUEST['sku'][$i]);
        $data = $this->product->load($productId);
  
        $dataVars = $data->getData();
        //die(var_dump($dataVars));
     //   die(var_dump($dataVars));
        if ( isset($dataVars['type_id']) && $dataVars['type_id'] == 'simple')
        {
            //if the product is simple one then add that product to the cart 
    //   $this->cart->addProduct($dataVars['product_id'],array('qty' => $_REQUEST['quantity'][$i]));
            $productIdArr = array();
            for($j=0;$j<$_REQUEST['quantity'][$i];$j++)
            {
                $productIdArr[$j] = $productId ;
            }
            
            $this->cart->addProductsByIds($productIdArr);
            $stat = $this->cart->save();
            $this->sessionGeneric->setData('suc_msg','Added the product to the cart.');
     
        //continue;
        }
        else if (isset($dataVars['type_id']) && $dataVars['type_id'] == 'configurable') // if the product is a configurable one then redirect the user to the product page
        {
            //die('yes this is a configurable product here');
            $productURL = $data->getProductUrl();
            header('Location:'.$productURL);
            exit();
        }
        else //if the product even does not exists in our store
        {
            //die('Ok so it has reached here');
            $this->sessionGeneric->setData('err_msg','This product with this SKU does not exists in the store.');
            header('Location:'.$_SERVER['HTTP_REFERER']);
            exit();
        }
        }
        }
         header('Location:'.$_SERVER['HTTP_REFERER']);
         exit();
        
    }
    
    public function urlBase(){
      return  $this->_storeManager->getStore()->getBaseUrl();        
    }
    
}
