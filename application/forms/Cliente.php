<?php

class Application_Form_Cliente extends Twitter_Bootstrap_Form_Horizontal
{

   public function init() {
      $this->setIsArray(true);
      //$this->setAttrib('onsubmit', "return TestaCPF($('#idNumber').val());");  

      $this->addElement('hidden', 'id');
      $this->addElement('hidden', 'id_client');
      $this->addElement('hidden', 'companyName');
      $this->addElement('hidden', 'profession');
      $this->addElement('hidden', 'fax');
      $this->addElement('hidden', 'id_country', array('value' => '30'));

      $this->addElement('text', 'birthDate', array(
          'id' => 'birthDate',
          'label' => 'Data de nascimento *:',
          'validators' => array('Date')
      ));

      $this->addElement('text', 'idNumber', array(
          'id' => 'idNumber',
          'label' => 'CPF *:',
          'filters' => array('StringTrim', 'StripTags'),
          'validators' => array(
              array('StringLength', false, array(13, 14)),
          ),
          'required' => true
      ));

      $this->addElement('text', 'phone', array(
          'id' => 'phone',
          'label' => '<span style="color:#ff0000;font-weight:bold" rel="popover" data-title="O número do telefone deste campo deve ser o número da linha do titular da conta telefônica. Caso seja digitado o número incorreto o seguro não será cobrado e não haverá pagamento de comissão por venda.">Número da linha segurada Vivo Fixa * <i class="icon-question-sign"></i></span>:',
          'validators' => array(
              array('StringLength', false, array(14, 255))
          ),
          'required' => true,
          'escape' => true
      ));
      $element = $this->getElement('phone');
      $element->getDecorator('label')->setOption('escape', false);

      $this->addElement('text', 'confPhone', array(
          'id' => 'confPhone',
          'label' => 'Confirme o número da linha segurada Vivo Fixa:',
          'validators' => array(
              array('StringLength', false, array(14, 255))
          ),
          'required' => false,
          'escape' => true
      ));
      $element = $this->getElement('confPhone');
      $element->getDecorator('label')->setOption('escape', false);

      $this->addElement('text', 'email', array(
          'id' => 'email',
          'label' => 'E-mail *:',
          'class' => 'input-xxlarge',
          'filters' => array('StringTrim', 'StripTags'),
          'validators' => array(
              array('StringLength', false, array(0, 255)),
              array('EmailAddress', true,
                  array(
                      'allow' => Zend_Validate_Hostname::ALLOW_DNS,
                      'domain' => true,
                      'mx' => true,
                      'deep' => false,
                      'messages' => array(
                          Zend_Validate_EmailAddress::INVALID => null,
                          Zend_Validate_EmailAddress::INVALID_FORMAT => 'Formato do e-mail inválido',
                          Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'Formato do e-mail inválido',
                          Zend_Validate_EmailAddress::INVALID_MX_RECORD => 'E-mail inválido',
                          Zend_Validate_EmailAddress::INVALID_SEGMENT => null,
                          Zend_Validate_EmailAddress::DOT_ATOM => null,
                          Zend_Validate_EmailAddress::QUOTED_STRING => null,
                          Zend_Validate_EmailAddress::INVALID_LOCAL_PART => null,
                          Zend_Validate_EmailAddress::LENGTH_EXCEEDED => null,
                          Zend_Validate_Hostname::CANNOT_DECODE_PUNYCODE => null,
                          Zend_Validate_Hostname::INVALID => null,
                          Zend_Validate_Hostname::INVALID_DASH => null,
                          Zend_Validate_Hostname::INVALID_HOSTNAME => null,
                          Zend_Validate_Hostname::INVALID_HOSTNAME_SCHEMA => null,
                          Zend_Validate_Hostname::INVALID_LOCAL_NAME => null,
                          Zend_Validate_Hostname::INVALID_URI => null,
                          Zend_Validate_Hostname::IP_ADDRESS_NOT_ALLOWED => null,
                          Zend_Validate_Hostname::LOCAL_NAME_NOT_ALLOWED => null,
                          Zend_Validate_Hostname::UNDECIPHERABLE_TLD => null,
                          Zend_Validate_Hostname::UNKNOWN_TLD => null,
                      )
                  )
              )
          )
      ));

      $this->addElement('checkbox', 'enableEmailOptin', array(
          'id' => 'enableEmailOptin',
          'label' => 'Deseja receber informações via Email:',
          'required' => false,
      ));

      $this->addElement('text', 'fullName', array(
          'id' => 'fullName',
          'label' => 'Nome completo *',
          'class' => 'focused input-xxlarge',
          'filters' => array('StringTrim', 'StripTags'),
          'validators' => array(
              array('regex', false, array(
                  'pattern'   => "/^([a-zA-ZéúíóáÉÚÍÓÁèùìòàÈÙÌÒÀõãñÕÃÑêûîôâÊÛÎÔÂëÿüïöäËYÜÏÖÄ'-]+\s+){1,4}[a-zA-zéúíóáÉÚÍÓÁèùìòàÈÙÌÒÀõãñÕÃÑêûîôâÊÛÎÔÂëÿüïöäËYÜÏÖÄ'-]+$/i",
                  'messages'  =>  'Digite o nome completo'))
          ),
          'required' => true
      ));

      $this->addElement('select', 'gender', array(
          'id' => 'gender',
          'label' => 'Sexo *:',
          'required' => true,
          "multiOptions" => array(
              "" => "Indeterminado",
              "1" => "Masculino",
              "2" => "Feminino"
          )
      ));

      $this->addElement('select', 'maritalStatus', array(
          'id' => 'maritalStatus',
          'label' => 'Estado civil *:',
          'required' => true,
          "multiOptions" => array(
              "" => "Não declarado",
              "1" => "Solteiro(a)",
              "2" => "Casado(a)",
              "3" => "Viúvo(a)",
              "4" => "Separado(a) judicialmente",
              "5" => "Divorciado(a)",
              "6" => "Desquitado(a)",
              "7" => "Companheiro(a)"
          )
      ));

      $this->addElement('text', 'brazilianGeneralRegistration', array(
          'id' => 'brazilianGeneralRegistration',
          'label' => 'RG *:',
          'filters' => array('StringTrim', 'StripTags'),
          'required' => true
      ));

      $this->addElement('text', 'zipCode', array(
          'id' => 'zipCode',
          'label' => 'CEP *:',
          'required' => true
      ));

      $this->addElement('text', 'address', array(
          'id' => 'address',
          'label' => 'Endereço:',
          'class' => 'input-xxlarge',
          'validators' => array(
              array('StringLength', false, array(0, 255))
          ),
          'required' => false
      ));

      $this->addElement('text', 'number', array(
          'id' => 'number',
          'label' => 'Número:',
          'required' => false
      ));

      $this->addElement('text', 'complement', array(
          'id' => 'complement',
          'label' => 'Complemento:'
      ));

      $this->addElement('text', 'neighborhood', array(
          'id' => 'neighborhood',
          'label' => 'Bairro:',
          'required' => false
      ));

      $this->addElement('text', 'city', array(
          'id' => 'city',
          'label' => 'Cidade:',
          'required' => false
      ));

      $this->addElement('select', 'stateUF', array(
          'id' => 'stateUF',
          'label' => 'Estado:',
          "multiOptions" => array(
              ""   => " -- ",
              "AC" => "Acre",
              "AL" => "Alagoas",
              "AM" => "Amazonas",
              "AP" => "Amapá",
              "BA" => "Bahia",
              "CE" => "Ceará",
              "DF" => "Distrito Federal",
              "ES" => "Espirito Santo",
              "GO" => "Goiás",
              "MA" => "Maranhão",
              "MG" => "Minas Gerais",
              "MS" => "Mato Grosso do Sul",
              "MT" => "Mato Grosso",
              "PA" => "Pará",
              "PB" => "Paraíba",
              "PE" => "Pernambuco",
              "PI" => "Piauí",
              "PR" => "Paraná",
              "RJ" => "Rio de Janeiro",
              "RN" => "Rio Grande do Norte",
              "RO" => "Rondônia",
              "RR" => "Roraima",
              "RS" => "Rio Grande do Sul",
              "SC" => "Santa Catarina",
              "SE" => "Sergipe",
              "SP" => "São Paulo",
              "TO" => "Tocantins"
          ),
          'required' => false
      ));

      $this->addElement('text', 'mobile', array(
          'id' => 'mobile',
          'label' => 'Celular:',
          'validators' => array(
              array('StringLength', false, array(14, 255))
          )
      ));

      $this->addElement('checkbox', 'enableMobileOptin', array(
          'id' => 'enableMobileOptin',
          'label' => 'Deseja receber informações via SMS:',
          'required' => false,
      ));

      $this->addElement('button', 'submit', array(
          'label' => 'Salvar cadastro',
          'type' => 'submit',
          'id' => 'submitButton',
          'class' => 'btn btn-success'
      ));

      $this->addElement('button', 'button', array(
          'label' => 'Cancelar Venda',
          'type' => 'button',
          'class' => 'btn btn-danger',
          'id' => 'btnCancelarFormCliente',
          "onclick" => "javascript:btn_cancelar();"
      ));

      $this->addDisplayGroup(
          array('button', 'submit'), 'actions', array(
              'disableLoadDefaultDecorators' => true,
              'decorators' => array('Actions')
          )
      );
   }

}