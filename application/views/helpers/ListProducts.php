<?php

class Zend_View_Helper_ListProducts extends Zend_View_Helper_Abstract
{

   public function listProducts($tipo = null) {
      if ($tipo == 'conta-protegida') {
         return $this->listProductsCp();
      }

      if ($tipo == 'residencial') {
         return $this->listProductsRes();
      }

      $produtos = Zend_Controller_Action_HelperBroker::getStaticHelper('Client');
      return $produtos->listProducts();
   }

   public function listProductsCp() {
      $produtos = Zend_Controller_Action_HelperBroker::getStaticHelper('Client');

      $arrRetorno = array();

      foreach ($produtos->listProducts() as $k => $v) {
         if ($v['group'] == 'conta-protegida') {
            $arrRetorno[$k] = $v['name'] . ' | '. $v['plano'] . ' | '. $v['valor'];
         }
      }

      return $arrRetorno;
   }

   public function listProductsRes() {
      $produtos = Zend_Controller_Action_HelperBroker::getStaticHelper('Client');

      $arrRetorno = array();

      foreach ($produtos->listProducts() as $k => $v) {
         if ($v['group'] == 'residencial') {
            $arrRetorno[$k] = $v['name'] . ' | '. $v['plano'] . ' | '. $v['valor'];
         }
      }

      return $arrRetorno;
   }

}

?>