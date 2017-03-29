<?php
class Zend_View_Helper_VerificaExpiracaoContrato extends Zend_View_Helper_Abstract
{
   function verificaExpiracaoContrato($dt){

      $date = new Zend_Date($dt);
      $date->add(3, Zend_Date::YEAR);
      $dt2 = $date->get('YYYY-MM-dd 00:00:00');


      $d1 = new Zend_Date();    
      $d2 = new Zend_Date($dt2);
      $diff = $d2->sub($d1)->toValue();

      if ($diff < 0){
         return true;
      } else {
         return false;
      }
      
   }
}
?>
