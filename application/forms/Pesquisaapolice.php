<?php

class Application_Form_Pesquisaapolice extends Twitter_Bootstrap_Form_Inline
{

   public function init() {
        $this->setIsArray(true);
        //$this->setElementsBelongTo('bootstrap');

        $this->addElement('text', 'apolice', array(
            'id'        => 'apolice',
            'prepend'   => 'Apólice',
            'class'     => 'focused input-medium'
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