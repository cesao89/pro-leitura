<?php
/*
 * Helper para tratar visualição de Ativo e Inativo do usuário
 */
class Zend_View_Helper_StatusUser extends Zend_View_Helper_Abstract
{
   /*
    * Método statusUser
    * param: status do usuário ( id_user )
    * descrição: define o status do usuário
    */
   function statusUser($idx = null){
      $idx = intval($idx);
      $arr = array(
                     1 => '<font color="green">ativo</font>',
                     -1 => '<font color="red">inativo</font>',
      );
      if ($idx != null){
         return $arr[$idx];
      } else {
         return $arr;
      }
   }
   
}

?>
