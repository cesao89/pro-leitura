<?php

class Zend_View_Helper_BarraProgresso extends Zend_View_Helper_Abstract
{
   function barraProgresso($valor, $total){
      if ($valor && $total){
         return ceil((intval($valor) * 100) / intVal($total));
      }
      return 0;
   }
}

?>
