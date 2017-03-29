<?php

/*
 * Helper: hasPerm
 * author: Carlos E Rizzo (carlos.rizzo@fsvas.com)
 * descrição: Shortcut para Model Auth -> hasPerm
 */

class Zend_View_Helper_HasPerm extends Zend_View_Helper_Abstract
{
   /*
    * Método para verificar se atendente tem permissão
    * param1: array de objetos
    * param2: permissão necessária
    * * return: true / false
    */

   public function hasPerm($list_perms, $perm) {
      $auth = new Application_Model_Auth();
      return $auth->hasPerm($list_perms, $perm);
   }

}

?>