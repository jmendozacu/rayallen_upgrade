<?php
namespace Kensium\TrackOrder\Block\View;
class Plugin
{
    public function afterGetBackUrl($subject, $result)
    {

        return $subject->getUrl('sales/guest/form');
    }
}