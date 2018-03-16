<?php
namespace Kensium\ListCategory\Block;

class ListCategory extends \Magento\Framework\View\Element\Template
{
    protected $categoryFactory;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->categoryFactory=$categoryFactory;
        parent::__construct($context, $data);

    }

    public function getInfo()
    {
        $category=$this->categoryFactory->create()->load(132);
        $childCategories=$category->getChildrenCategories();//->limit(8);
        $data=array();
        $i=0;
        foreach($childCategories as $cat) {
            if($i==10) break;
            $category=$this->categoryFactory->create()->load($cat->getId());
            if($category->getName()=='Tactical Polos & Shirts'){
                $name='Polos & Shirts';
            }else if($category->getName()=='Tactical Bags and Packs')
            {
                $name='Bags and Packs';
            }else
                if($category->getName()=='Tactical Gear and Accessories')
                {
                    $name='Tactical Gear';
                }else
                {
                    $name=$category->getName();
                }
            $data[]=array('name' =>$name,'url' =>$category->getUrl(),'image'=>$category->getImageUrl());
            $i++;
        }
        return $data;
    }

}
?>
