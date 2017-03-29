<?php

class Application_Form_Pesquisacpf extends Twitter_Bootstrap_Form_Inline
{

   public function init() {
      $this->setIsArray(true);
      //$this->setElementsBelongTo('bootstrap');
      //$this->setAttrib('onsubmit', "return TestaCPF($('#cpf').val());");  

      $this->addElement('text', 'cpf', array(
          'id' => 'cpf',
          'prepend' => 'CPF',
          'class' => 'focused input-medium',
          'validators' => array(
              array('StringLength', false, array(14, 14))
          )
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