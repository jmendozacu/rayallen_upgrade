<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\FastOrder\Block\Product\Renderer;

use Magento\Swatches\Block\Product\Renderer\Configurable as SwatchesConfigurable;

class Configurable extends SwatchesConfigurable
{
    /**
     * Path to template file with Swatch renderer for fastorder module.
     */
	const FASTORDER_RENDERER_TEMPLATE = 'Bss_FastOrder::configurable.phtml';

    /**
     * Return renderer template
     *
     * @return string
     */
    protected function getRendererTemplate()
    {
		return self::FASTORDER_RENDERER_TEMPLATE;
    }
}
