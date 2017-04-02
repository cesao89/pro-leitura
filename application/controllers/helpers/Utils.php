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

    function mask($val, $mask)
    {
        if(empty($val) || empty($mask))
            return $val;

        $needMask = preg_replace('/[^#]/', '', $mask);
        $maskared = '';
        $k = 0;

        if(strlen($needMask) != strlen($val))
            $val = str_pad($val, strlen($needMask), 0, STR_PAD_LEFT);

        for($i=0; $i <= strlen($mask)-1; $i++){
            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    function fullNameCountry($sigla3, $explode = false){
        if($explode){
            $siglas = explode($explode, $sigla3);
        } else {
            $siglas = $sigla3;
        }
        $json = file_get_contents(APPLICATION_PATH .'/../public/js/paises.json');
        $paises = json_decode($json);

        foreach ($paises as $pais){
            if(is_array($siglas)){
                $correctName = null;
                foreach ($siglas as $sigla){
                    $correctName .= (!empty($correctName)) ? ', ' : null;
                    $correctName .= $this->fullNameCountry($sigla);
                }
                return $correctName;
            } else {
                if($pais->sigla3 == $siglas){
                    return $pais->nome;
                }
            }
        }

        return $siglas;
    }
}