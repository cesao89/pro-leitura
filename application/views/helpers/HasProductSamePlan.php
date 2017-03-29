<?php
class Zend_View_Helper_HasProductSamePlan extends Zend_View_Helper_Abstract
{
   function hasProductSamePlan($productId){
      
      return $this->listProduct($productId);
      
   }
   
   private function listProduct(){
      
   }
   
}

?>
