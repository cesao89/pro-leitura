<?php

/**
 * Class Zend_View_Helper_FullStateName
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Zend_View_Helper_FullStateName extends Zend_View_Helper_Abstract
{
    function fullStateName($sigla, $explode = false){
        if($explode){
            $siglas = explode($explode, $sigla);
        } else {
            $siglas = $sigla;
        }
        $json = file_get_contents(APPLICATION_PATH .'/../public/js/estados_cidades.json');
        $estados = json_decode($json);

        foreach ($estados as $estado){
            if(is_array($siglas)){
                $correctName = null;
                foreach ($siglas as $sigla){
                    $correctName .= (!empty($correctName)) ? ', ' : null;
                    $correctName .= $this->fullStateName($sigla);
                }
                return $correctName;
            } else {
                if($estado->sigla == $siglas){
                    return $estado->nome;
                }
            }
        }

        return $siglas;
    }
}