<?php

/**
 * Class Zend_Controller_Action_Helper_Utils
 */
class Zend_Controller_Action_Helper_Utils extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @param $text
     * @return string
     */
    public function escape($text)
    {
        return addslashes($text);
    }

    /**
     * Função que faz o escape das strings num array
     * @param $values
     * @return array
     */
    function escape_array($values)
    {
        if (!get_magic_quotes_gpc()) {
            $values = $this->array_map_recursive("addslashes", $values);
        }

        $arr_retorno = array();
        if (is_array($values)){
            foreach($values as $key => $item){
                if (is_array($item)){
                    $arr_retorno[$key] = $this->escape_array($item);
                } else {
                    $arr_retorno[$key] = trim($item);
                }
            }
        }
        return $arr_retorno;
    }


    /**
     * Função que tira o escape das strings num array recursivamente
     * @param $func
     * @param $arr
     * @return array
     */
    function array_map_recursive($func, $arr)
    {
        $result = array();
        do {
            $key = key($arr);
            if (is_array(current($arr))) {
                $result[$key] = array_map($func, $arr[$key]);
            } else {
                $result[$key] = $func(current($arr));
            }
        } while (next($arr) !== false);
        return $result;
    }

    function dateFormat($dateIN, $dateOUT='Y-m-d')
    {
        $date = new DateTime($dateIN);
        return $date->format($dateOUT);
    }
}