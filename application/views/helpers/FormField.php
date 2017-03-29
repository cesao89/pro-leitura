<?php

/*
 * Helper: FormField
 * author: Carlos E Rizzo (carlos.rizzo@fsvas.com)
 * decrição: Helper para gerar formulário ( um campo a um )
 */

class Zend_View_Helper_FormField extends Zend_View_Helper_Abstract
{
   /*
    * Propriedade referente a Model
    */

   protected $fields;
   /*
    * Array de erros
    */
   protected $errors;

   /*
    * Método que carrega o campo
    * param1: array de propriedades da Model
    * param2: campo necessário ( nome da propriedade )
    * param3: true/false - carregar erros automaticamente
    * return: retorna para renderização do campo - _render
    */

   public function formField($fields, $field, $errors = true) {
      $this->fields = $fields;
      $this->errors = $errors;
      if ($field) {
         foreach ($this->fields as $key => $item) {
            if ($field == $key) {
               return $this->_render($field);
            }
         }
      }
      return '';
   }

   /*
    * Método que renderiza o campo
    * param: array da propriedade requisitada
    * return: retorna para renderização do tipo do campo / ou vazio se nao encontrar
    */

   private function _render($field) {
      $arr = $this->fields[$field];
      if (isset($arr['form']['type'])) {
         switch ($arr['form']['type']) {
            case 'textfield':
               return $this->_textfield($arr);
               break;
            case 'hidden':
               return $this->_hidden($arr);
               break;
            case 'textarea':
               return $this->_textarea($arr);
               break;
            case 'password':
               return $this->_password($arr);
               break;
            case 'checkbox':
               return $this->_checkbox($arr);
               break;
            case 'select':
               return $this->_select($arr);
               break;
            case 'radio':
               return $this->_radio($arr);
               break;
         }
      }
      return '';
   }

   /*
    * Método para renderizar TextField
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _textfield($arr) {
      $name = $arr['name'];
      if (isset($arr['value'])) {
         $value = $arr['value'];
      } else {
         $value = '';
      }
      if (isset($arr['size'])) {
         $maxlength = $arr['size'];
      } else {
         $maxlength = '';
      }
      if (isset($arr['form']['css'])) {
         $css = $arr['form']['css'];
      } else {
         $css = '';
      }
      if ($maxlength) {
         $maxlength = ' maxlength="' . $maxlength . '" ';
      }
      if ($css) {
         $css = ' class="' . $css . '" ';
      }
      $event = '';
      $call = '';
      if (isset($arr['form']['event'])) {
         $event = $arr['form']['event']['name'];
         if (isset($arr['form']['event']['call'])) {
            $call = $arr['form']['event']['call'];
         }
         $event = $event . '="' . $call . '"';
      }
      /*
       * Adiciona erro caso tenha
       */
      $msg_error = '';
      if (isset($arr['error'])) {
         $msg_error = $this->_error($arr['error']);
      }
      return '<input type="text" id="' . $name . '" name="' . $name . '" value="' . $value . '" ' . $maxlength . ' ' . $css . '  ' . $event . '/>' . $msg_error;
   }

   /*
    * Método para renderizar Hidden
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _hidden($arr) {
      $name = $arr['name'];
      if (isset($arr['value'])) {
         $value = $arr['value'];
      } else {
         $value = '';
      }
      return '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '" />';
   }

   /*
    * Método para renderizar textArea
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _textarea($arr) {
      $name = $arr['name'];
      if (isset($arr['value'])) {
         $value = $arr['value'];
      } else {
         $value = '';
      }

      if (isset($arr['size'])) {
         $maxlength = $arr['size'];
      } else {
         $maxlength = '';
      }
      if (isset($arr['form']['css'])) {
         $css = $arr['form']['css'];
      }
      $style = '';
      if (isset($arr['form']['style'])) {
         $style = ' style="' . $arr['form']['style'] . '"';
      }
      if ($maxlength) {
         $maxlength = ' maxlength="' . $maxlength . '" ';
      }
      if ($css) {
         $css = ' class="' . $css . '" ';
      }
      $event = '';
      if (isset($arr['form']['event'])) {
         $event = $arr['form']['event']['name'];
         if (isset($arr['form']['event']['call'])) {
            $call = $arr['form']['event']['call'];
         }
         $event = $event . '="' . $call . '"';
      }
      /*
       * Adiciona erro caso tenha
       */
      $msg_error = '';
      if (isset($arr['error'])) {
         $msg_error = $this->_error($arr['error']);
      }
      return '<textarea id="' . $name . '" name="' . $name . '" ' . $css . '  ' . $event . ' ' . $style . '>' . $value . '</textarea>' . $msg_error;
   }

   /*
    * Método para renderizar Password
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _password($arr) {
      $name = $arr['name'];
      if (isset($arr['size'])) {
         $maxlength = $arr['size'];
      } else {
         $maxlength = '';
      }
      if (isset($arr['form']['css'])) {
         $css = $arr['form']['css'];
      }
      if ($maxlength) {
         $maxlength = ' maxlength="' . $maxlength . '" ';
      }
      if ($css) {
         $css = ' class="' . $css . '" ';
      }
      $event = '';
      if (isset($arr['form']['event'])) {
         $event = $arr['form']['event']['name'];
         if (isset($arr['form']['event']['call'])) {
            $call = $arr['form']['event']['call'];
         }
         $event = $event . '="' . $call . '"';
      }
      /*
       * Adiciona erro caso tenha
       */
      $msg_error = '';
      if (isset($arr['error'])) {
         $msg_error = $this->_error($arr['error']);
      }
      return '<input type="password" id="' . $name . '" name="' . $name . '" ' . $maxlength . ' ' . $css . ' ' . $event . ' />' . $msg_error;
   }

   /*
    * Método para renderizar Checkbox
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _checkbox($arr) {
      $name = $arr['name'];
      $value = '';
      if (isset($arr['value'])) {
         $value = $arr['value'];
      }      
      if (isset($arr['list'])) {
         $lista = $arr['list'];
      }
      if (isset($arr['form']['css'])) {
         $css = $arr['form']['css'];
      }
      if ($css) {
         $css = ' class="' . $css . '" ';
      }
      $event = '';
      if (isset($arr['form']['event'])) {
         $event = $arr['form']['event']['name'];
         if (isset($arr['form']['event']['call'])) {
            $call = $arr['form']['event']['call'];
         }
         $event = $event . '="' . $call . '"';
      }
      if (is_array($lista)) {
         $arr = array();
         foreach ($lista as $k => $v) {
            $checked = '';
            if ($value == $k) {
               $checked = 'checked';
            }
            array_push($arr, '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="' . $k . '" ' . $checked . ' ' . $css . ' />');
         }
         /*
          * Adiciona erro caso tenha
          */
         $msg_error = '';
         if (isset($arr['error'])) {
            $msg_error = $this->_error($arr['error']);
         }
         return implode('<br />', $arr) . $msg_error;
      } else {
         return '';
      }
   }

   /*
    * Método para renderizar Select (combo)
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _select($arr) {
      $name = $arr['name'];
      if (isset($arr['value'])) {
         $value = $arr['value'];
      } else {
         $value = '';
      }
      if (isset($arr['form']['style'])) {
         $style = 'style = " ' . $arr['form']['style'] . '"';
      } else {
         $style = '';
      }

      if (isset($arr['list'])) {
         $lista = $arr['list'];
      }
      if (isset($arr['form']['css'])) {
         $css = $arr['form']['css'];
      }
      if ($css) {
         $css = ' class="' . $css . '" ';
      }
      $event = '';
      if (isset($arr['form']['event'])) {
         $event = $arr['form']['event']['name'];
         if (isset($arr['form']['event']['call'])) {
            $call = $arr['form']['event']['call'];
         }
         $event = $event . '="' . $call . '"';
      }

      if (is_array($lista)) {
         $r = '<select name="' . $name . '" ' . $css . ' id="' . $name . '" ' . $event . ' ' . $style . '>';
         foreach ($lista as $k => $v) {
            $selected = '';
            if ($value == $k) {
               $selected = 'selected=true';
            }
            $r .= '<option ' . $selected . ' value="' . $k . '" >' . $v . '</option>';
         }
         $r .= '</select>';
         /*
          * Adiciona erro caso tenha
          */
         $msg_error = '';
         if (isset($arr['error'])) {
            $msg_error = $this->_error($arr['error']);
         }
         return $r . $msg_error;
      } else {
         return '';
      }
   }

   /*
    * Método para renderizar Radio Button
    * param: array da propriedade referente a Model
    * return: render do campo
    */

   private function _radio($arr) {
      $name = $arr['name'];
      $value = $arr['value'];
      if (isset($arr['list'])) {
         $lista = $arr['list'];
      }
      if (isset($arr['form']['css'])) {
         $css = $arr['form']['css'];
      }
      if ($css) {
         $css = ' class="' . $css . '" ';
      }
      if (isset($arr['form']['event'])) {
         $event = $arr['form']['event']['name'];
         if (isset($arr['form']['event']['call'])) {
            $call = $arr['form']['event']['call'];
         }
         $event = $event . '="' . $call . '"';
      }
      if (is_array($lista)) {
         $arr = array();
         foreach ($lista as $k => $v) {
            $checked = '';
            if ($value == $k) {
               $checked = 'checked';
            }
            array_push($arr, '<input type="radio" name="' . $name . '" id="' . $name . '" 
																value="' . $value . '" ' . $checked . ' ' . $css . ' ' . $event . ' />');
         }
         /*
          * Adiciona erro caso tenha
          */
         $msg_error = '';
         if (isset($arr['error'])) {
            $msg_error = $this->_error($arr['error']);
         }
         return implode('<br />', $arr) . $msg_error;
      } else {
         return '';
      }
   }

   /*
    * Método para renderização de erro ( erro automático - FormField: param3: $errors )
    * param: String do ero
    * return: string para concatenar o erro
    */

   private function _error($err) {
      if ($this->errors) {
         return '<label class="error" generated="true"><b>* ' . $err . '</b></label>';
      } else {
         return '';
      }
   }

}

?>