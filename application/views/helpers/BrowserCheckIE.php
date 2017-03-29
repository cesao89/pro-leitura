<?php

class Zend_View_Helper_BrowserCheckIE extends Zend_View_Helper_Abstract {

    /** RETORNA FALSE SE NAVEGADOR FOR I.E, CASO CONTRÁRIO, RETORNA TRUE */
    public function browserCheckIE() {
        if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $_SERVER['HTTP_USER_AGENT'], $matched)) :
            return ($matched[1] === '8.0') ? false : true;
        endif;
        return false;
    }

}
