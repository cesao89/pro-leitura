<?php
/*
 * Helper para controlar botões de ação na view ( Cancelar Chave, Reenviar Certificado, Incluir Sinistro, Detalhes )
 */
class Zend_View_Helper_ActionHistory extends Zend_View_Helper_Abstract
{
   /*
    * Método actionHistory
    * param1: status da venda ( id_stageOfSale )
    * param2: ação tomada
    * return: true / false
    */
   function actionHistory($status, $action){
      if ($action){
         
         switch ($action){
            case 'detalhes':
               switch ($status){
                  case 1:
                     return false;
                  case 2:
                  case 3:
                  case 4:
                  case 5:
                  case 6:
                  case 7:
                  case 8:
                  case 9:
                  case 10:
                  case 11:
                  case 12:
                  case 13:
                  case 14:
                  case 15:
                  case 16:
                  case 17:
                  case 18:
                  case 19:
                  case 20:
                  case 21:
                  case 22:
                  case 23:
                  case 24:
                  case 25:
                     return true;
                     
                  default:
                     return false;
               }
            case 'sinistro':
               switch ($status){
                  case 1:
                  case 2:
                  case 8:
                  case 9:
                  case 10:
                  case 11:
                  case 12:
                  case 13:
                  case 14:
                  case 15:
                  case 16:
                  case 17:
                  case 19:
                  case 20:
                  case 21:
                  case 22:
                  case 24:
                  case 25:
                  case 3:
                  case 4:
                  case 5:
                  case 6:
                  case 7:
                     return false;
                  case 18:
                  case 23:
                     return true;
                     
                  default:
                     return false;
               }
            case 'certificado':
               switch ($status){
                  case 1:
                  case 2:
                  case 8:
                  case 9:
                  case 10:
                  case 11:
                  case 12:
                  case 13:
                  case 14:
                  case 15:
                  case 16:
                  case 17:
                  case 19:
                  case 20:
                  case 21:
                  case 22:
                  case 24:
                  case 25:
                  case 3:
                  case 4:
                  case 5:
                  case 6:
                  case 7:
                     return false;
                  case 18:
                  case 23:
                     return true;
                     
                  default:
                     return false;
               }
            case 'cancelamento':
               switch ($status){
                  case 1:
                  case 2:
                  case 8:
                  case 9:
                  case 10:
                  case 11:
                  case 12:
                  case 13:
                  case 14:
                  case 15:
                  case 16:
                     return false;
                  case 17:
                  case 18:
                  case 19:
                  case 20:
                  case 21:
                  case 22:
                  case 23:
                  case 24:
                  case 25:
                      # ADD EM 14/06 - por Mallon
                  case 27:
                  case 28:
                  case 29:
                  case 30:
                  case 31:
                  case 32:
                  case 33:
                  case 34:
                  case 35:
                      # FIM ADD
                  case 3:
                  case 4:
                  case 5:
                  case 6:
                  case 7:
                     return true;
                     
                  default:
                     return false;
               }
            default:
               return false;
            
         }
         
      } else {
         return false;
      }
   }
   
}

?>
