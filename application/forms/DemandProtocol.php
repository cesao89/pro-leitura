<?php

class Application_Form_DemandProtocol extends Twitter_Bootstrap_Form_Inline
{

   public function init() {
      $this->setIsArray(true);
      $this->addElement('text', 'protocol', array(
          'id' => 'protocol',
          'prepend' => 'Protocolo de Atendimento',
          'class' => 'focused input-large',
      ));

      $this->addElement('button', 'submit', array(
          'label' => 'Consultar',
          'type' => 'submit',
          'buttonType' => 'success',
          'escape' => false,
          'onclick' => "processModal('show', 'Efetuando pesquisa, aguarde...');"
      ));
   }

}