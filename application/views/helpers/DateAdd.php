<?php
class Zend_View_Helper_DateAdd extends Zend_View_Helper_Abstract
{
   function dateAdd($dt, $incr=0){
      if ($dt){
         $date = new Zend_Date($dt);
         $date->add($incr, Zend_Date::YEAR);
         return $date->get('YYYY-MM-dd 00:00:00');      
      }
      return '';
   }
}
?>
