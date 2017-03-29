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
class Zend_View_Helper_Mascaratelefone extends Zend_View_Helper_Abstract
{
   function mascaratelefone($val){
      if(strlen($val)==10){
         return "(" . substr($val, 0, 2) . ") " . substr($val, 2,4) . "-" . substr($val, 6);
      }
      return "(" . substr($val, 0, 2) . ") " . substr($val, 2,5) . "-" . substr($val, 7);
   }
}

?>
