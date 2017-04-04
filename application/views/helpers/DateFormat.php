<?php

/**
 * Class Zend_View_Helper_DateFormat
 */
class Zend_View_Helper_DateFormat extends Zend_View_Helper_Abstract
{
    function dateFormat($data, $output='d/m/Y H:i:s'){
        $newDate = new DateTime($data);
        return $newDate->format($output);
   }
}