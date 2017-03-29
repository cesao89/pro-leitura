<?php

class Zend_View_Helper_ResultadoDeOperacoes extends Zend_View_Helper_Abstract
{

    public function resultadoDeOperacoes()
    {
        $resultado = '';
        $flashMessenger = Zend_Controller_Action_HelperBroker::getExistingHelper('FlashMessenger');

        $mensagens = $flashMessenger->getMessages() + $flashMessenger->getCurrentMessages();
        $flashMessenger->clearCurrentMessages();

        foreach ($mensagens as $linha) {
            foreach ($linha as $tipo => $mensagem) {
                $resultado .= '<div class="alert alert-' . $tipo . '"><button data-dismiss="alert" class="close"></button>' . $mensagem . '</div>';
            }
        }

        if ($resultado != '') {
            $resultado .= "\r\n\r\n";
            $resultado .= '<script type="text/javascript">
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(5000, function(){ $(this).remove(); });
                }, 300000);
            </script>';
        }

        return $resultado;
    }
}