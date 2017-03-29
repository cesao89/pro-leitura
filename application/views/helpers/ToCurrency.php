<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Convertetelefone
 *
 * @author fabio.gabbay
 */
class Zend_View_Helper_ToCurrency extends Zend_View_Helper_Abstract
{
   function toCurrency($val){
      $currency = new Zend_Currency();
      if ($val) {
         return $currency->toCurrency($val);
      } else {
         return $val;
      }
      
   }
}

?>
