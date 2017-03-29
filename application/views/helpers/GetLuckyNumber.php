<?php
/*
 * Helper 
 */
class Zend_View_Helper_GetLuckyNumber extends Zend_View_Helper_Abstract
{
   /*
    * Método getLuckyNumber
    */
   function getLuckyNumber($uid = null){
      try{
         $clienteSoap = new Application_Model_ClienteSoap();
         $num = $clienteSoap->getLuckNumberFromSaleUID($uid);
         if ($num)
            return str_pad(str_replace(array('[', ']'), '', $num), 8, "0", STR_PAD_LEFT);
         
      } catch (Exception $e){
         return "nda";
      }
      
   }
   
}

?>
