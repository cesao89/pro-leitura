<?php
class Zend_View_Helper_DateFormatViewAdd extends Zend_View_Helper_Abstract
{
   function dateFormatViewAdd($dt, $incr=0){
      if ($dt){
         $date = new Zend_Date($dt);
         $date->add($incr, Zend_Date::YEAR);
         return $date->get("dd/MM/YYYY");      
      }
      return '';
   }
}
?>
