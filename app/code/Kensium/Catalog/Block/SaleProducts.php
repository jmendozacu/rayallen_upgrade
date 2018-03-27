<?php
namespace Kensium\Catalog\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class SaleProducts extends \Magento\Catalog\Block\Product\ListProduct
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
    )
    {

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    public function getProductCollection()
    {
       // $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Store\Model\StoreManagerInterface $manager */
       // $manager = $om->get('Magento\Store\Model\StoreManagerInterface');
        $collection = $this->_productCollectionFactory->create();
        $collection->addFinalPrice()
            ->getSelect()
            ->where('price_index.final_price < price_index.price');
        return $collection;
    }

    public function getProductObject($id)
    {
        $product = $this->productFactory->create()->load($id);
        return $product;
    }
}

?>