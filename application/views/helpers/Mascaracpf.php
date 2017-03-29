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
class Zend_View_Helper_Mascaracpf extends Zend_View_Helper_Abstract
{
   function mascaracpf($val){
      return substr($val, 0, 3) . "." . substr($val, 3,3) . "." . substr($val, 6,3) . "-" . substr($val, 9);
   }
}

?>
