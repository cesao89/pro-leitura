<?php

/**
 * Class Zend_View_Helper_FullCountryName
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Zend_View_Helper_FullCountryName extends Zend_View_Helper_Abstract
{
    function fullCountryName($sigla3, $explode = false){
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
                    $correctName .= $this->fullCountryName($sigla);
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