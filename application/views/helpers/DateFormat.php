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
class Zend_View_Helper_DateFormat extends Zend_View_Helper_Abstract
{
   function dateFormat($data, $format='AAAA-MM-DD'){
      if ($data) {
         if (strstr($data, '-')) {
            $formato = 'AAAA-MM-DD';
         }
         if (strstr($data, ' ')) {
            list($data, $hora) = explode(' ', $data);
         }
         switch ($formato) {
            case 'DD-MM-AAAA':
               list($d, $m, $a) = explode('-', $data);
               break;
            case 'DD/MM/AAAA':
               list($d, $m, $a) = explode('/', $data);
               break;
            case 'AAAA/MM/DD':
               list($a, $m, $d) = explode('/', $data);
               break;
            case 'AAAA-MM-DD':
               list($a, $m, $d) = explode('-', $data);
               break;
            case 'AAAA/DD/MM':
               list($a, $d, $m) = explode('/', $data);
               break;
            case 'AAAA-DD-MM':
               list($a, $d, $m) = explode('-', $data);
               break;
            case 'MM-DD-AAAA':
               list($m, $d, $a) = explode('-', $data);
               break;
            case 'MM/DD/AAAA':
               list($m, $d, $a) = explode('/', $data);
               break;
            case 'AAAAMMDD':
               $a = substr($data, 0, 4);
               $m = substr($data, 4, 2);
               $d = substr($data, 6, 2);
               break;

            case 'AAAADDMM':
               $a = substr($data, 0, 4);
               $d = substr($data, 4, 2);
               $m = substr($data, 6, 2);
               break;

            default:
               return false;
               break;
         }
         if (checkdate($m, $d, $a)) {
            //return $d . '/' . $m . '/' . $a . ' ' . $hora;
            return $d . '/' . $m . '/' . $a;
         } else {
            return 'Data inválida';
         }
      }
      return '';
   }
}

?>
