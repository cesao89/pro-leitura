<?php

class Application_Form_Pesquisatelefone extends Twitter_Bootstrap_Form_Inline
{

    public function init() {
        $this->setIsArray(true);
        //$this->setElementsBelongTo('bootstrap');

        $this->addElement('text', 'phone', array(
            'id'        => 'phone',
            'prepend'   => 'Número da linha segurada Vivo Fixa',
            'class'     => 'focused input-medium',
        ));

        $this->addElement('button', 'submit', array(
            'label'         => 'Consultar',
            'type'          => 'submit',
            'buttonType'    => 'success',
            'escape'        => false,
            'onclick'       => "processModal('show', 'Efetuando pesquisa, aguarde...');"
        ));
    }

}