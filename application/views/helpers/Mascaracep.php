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
class Zend_View_Helper_Mascaracep extends Zend_View_Helper_Abstract
{
   function mascaracep($val){
      return substr($val, 0, 5) . "-" . substr($val, 5);
   }
}

?>
