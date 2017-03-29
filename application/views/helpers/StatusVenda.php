<?php

/*
 * Helper para tratar visualição de Ativo e Inativo
 */

class Zend_View_Helper_StatusVenda extends Zend_View_Helper_Abstract {
    /*
     * Método statusVeda
     * param: status da venda ( id_stageOfSale )
     * descrição: define o status da venda
     */

    function statusVenda($idx = null) {
        $idx = intval($idx);
        $arr = array(
            1 => $this->statusVendaFormat('aguardando'),
            2 => $this->statusVendaFormat('inativo'),
            3 => $this->statusVendaFormat('aguardando'),
            4 => $this->statusVendaFormat('aguardando'),
            5 => $this->statusVendaFormat('aguardando'),
            6 => $this->statusVendaFormat('aguardando'),
            7 => $this->statusVendaFormat('aguardando'),
            8 => $this->statusVendaFormat('inativo'),
            9 => $this->statusVendaFormat('inativo'),
            10 => $this->statusVendaFormat('inativo'),
            11 => $this->statusVendaFormat('inativo'),
            12 => $this->statusVendaFormat('inativo'),
            13 => $this->statusVendaFormat('aguardando'),
            14 => $this->statusVendaFormat('inativo'),
            15 => $this->statusVendaFormat('inativo'),
            16 => $this->statusVendaFormat('inativo'),
            17 => $this->statusVendaFormat('inativo'),
            18 => $this->statusVendaFormat('ativo-legado'), // Pagou
            19 => $this->statusVendaFormat('aguardando'),
            20 => $this->statusVendaFormat('aguardando'),
            21 => $this->statusVendaFormat('aguardando'),
            22 => $this->statusVendaFormat('aguardando'),
            23 => $this->statusVendaFormat('ativo'), // Pagou
            24 => $this->statusVendaFormat('aguardando'),
            25 => $this->statusVendaFormat('aguardando'),
            # ADD EM 14/06 - por Mallon
            27 => $this->statusVendaFormat('aguardando'),
            28 => $this->statusVendaFormat('aguardando'),
            29 => $this->statusVendaFormat('ativo'),
            30 => $this->statusVendaFormat('ativo'),
            31 => $this->statusVendaFormat('aguardando'),
            32 => $this->statusVendaFormat('aguardando'),
            33 => $this->statusVendaFormat('aguardando'),
            34 => $this->statusVendaFormat('aguardando')
            # FIM ADD
        );
        if ($idx != null) {
            return $arr[$idx];
        } else {
            return $arr;
        }
    }

    /*
     * Método statusVendaFormat
     * param: status ( ativo / inativo )
     * return: formata visualização do status
     */

    function statusVendaFormat($idx = null) {
        $arr = array(
            'aguardando' => "<div align='center' style='color:blue;'><b>aguardando pagamento</b></div>",
            'inativo' => "<div align='center' style='color:red;'><b>cancelado</b></div>",
            'ativo' => "<div align='center' style='color:green;'><b>ativo</b></div>",
            'ativando' => "<div align='center' style='color:green;'><b>ativando</b></div>",
            'ativo-legado' => "<div align='center' style='color:green;'><b>ativo (legado)</b></div>",
        );
        if ($idx != null) {
            return $arr[$idx];
        } else {
            return $arr;
        }
    }

    /*
     * Método statusVendaAjax
     * descrição: Utilizada para definir status geral do cliente
     * param: status da venda ( id_stageOfSale )
     * return: true / false
     */

    function statusVendaAjax($idx = null) {
        $idx = intval($idx);
        $arr = array(
            1 => true,
            2 => false,
            3 => true,
            4 => true,
            5 => true,
            6 => true,
            7 => true,
            8 => false,
            9 => false,
            10 => false,
            11 => false,
            12 => false,
            13 => true,
            14 => false,
            15 => false,
            16 => false,
            17 => true,
            18 => true,
            19 => true,
            20 => true,
            21 => true,
            22 => true,
            23 => true,
            24 => true,
            25 => true,
            26 => true,
            27 => true,
            28 => true,
            29 => true,
            30 => true,
            31 => true,
            32 => true,
            33 => true,
            34 => true,
        );
        if ($idx != null) {
            return $arr[$idx];
        } else {
            return $arr;
        }
    }

}

?>
