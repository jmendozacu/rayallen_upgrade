<?php

/**
 * Copyright © 2017 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\DataFeedManager\Helper;

/**
 *
 * @author Paul
 */
interface AttributesInterface
{

    public function proceedGeneric($attributeCall,
            $model,
            $options,
            $product,
            $reference);
}
