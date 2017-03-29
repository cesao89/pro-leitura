<?php
class Zend_View_Helper_DateFormatView extends Zend_View_Helper_Abstract
{
   function dateFormatView($dt){
      if ($dt){
         $date = new Zend_Date($dt);
         return $date->get("dd/MM/YYYY");      
      }
      return '';
   }
}
?>
