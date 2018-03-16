<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Authnetcim\Model\Ach;

/**
 * Factory class for @see \ParadoxLabs\Authnetcim\Model\Ach\Card
 */
class CardFactory extends \ParadoxLabs\Authnetcim\Model\CardFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = '\\ParadoxLabs\\Authnetcim\\Model\\Ach\\Card'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
