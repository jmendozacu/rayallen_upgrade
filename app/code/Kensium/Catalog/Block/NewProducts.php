<?php
namespace Kensium\Catalog\Block;
use Magento\Catalog\Api\CategoryRepositoryInterface;
class NewProducts extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $_productCollectionFactory;
    protected $productFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context,$postDataHelper,$layerResolver,$categoryRepository,$urlHelper, $data);
    }

    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $todayDate  = date('Y-m-d', time());
		$storeId = $this->getCurrentStoreId(); 
        $collection->addStoreFilter($storeId);
		$collection->addAttributeToFilter('visibility' , '4');
	$collection->addAttributeToFilter('status', 1);
        $collection->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate));
        return $collection;
    }
    public function getProductObject($id){
        $product=$this->productFactory->create()->load($id);
        return $product;
    }
    /**
     * Get current store id.
     *
     * @return string
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

}
?>
