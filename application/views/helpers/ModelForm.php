<?php

/*
 * Helper: ModelForm
 * author: Carlos E Rizzo (carlos.rizzo@fsvas.com)
 * requisito: FormField
 * decrição: Helper para gerar formulário
 */

class Zend_View_Helper_ModelForm extends Zend_View_Helper_Abstract
{
   /*
    * Propriedade referente a Model
    */

   protected $fields;

   /*
    * Método para gerar formulário
    * param1: array de propriedades da Model
    * param2: array de configuração
    *        - css, style, cellpading, cellspacing, exclude( array de campos para não mostrar )
    */

   public function modelForm($fields, $config = null) {
      $css = '';
      $style = '';
      $cellpadding = 4;
      $cellspacing = 4;
      $exclude = '';
      // Define config
      if ($config) {
         if (isset($config['css'])) {
            $css = 'class = "' . $config['css'] . '"';
         }
         if (isset($config['style'])) {
            $style = 'style = "' . $config['style'] . '"';
         }
         if (isset($config['cellpadding'])) {
            $cellpadding = $config['cellpadding'];
         }
         if (isset($config['cellspacing'])) {
            $cellspacing = $config['cellspacing'];
         }
         if (isset($config['exclude'])) {
            $exclude = $config['exclude'];
         }
      }
      
      // Define propriedades
      $this->fields = $fields;
      if (is_array($this->fields)) {
         $r = '<table cellpadding="' . $cellpadding . '" cellspacing="' . $cellspacing . '" ' . $css . ' ' . $style . ' >';
         // Campos hidden
         $hidden = '';
         // Varre propriedades renderizando o formulário com o Helper FormField
         foreach ($this->fields as $key => $item) {
            if ($this->fields[$key]['form'] != false) {
               if (!strstr("," . $exclude . ",", "," . $key . ",")) {

                  // Verifica se é hidden
                  $show = true;
                  if (isset($this->fields[$key]['form']['type'])) {
                     if ($this->fields[$key]['form']['type'] == 'hidden') {
                        // adiciona campo hidden
                        $hidden .= $this->view->formField($this->fields, $key);
                        $show = False;
                     }
                  }
                  // Verifica se é para mostrar
                  if ($show) {
                     $r .= '
                        <tr>
                            <td  style="padding-left:5px;text-align:right" >
                                <div class="control-group">
                                    <label class="control-label" id="label_'. $item['name'] .'" for="'. $item['name'] .'">'. $item['title'] .':</label>
                                </div>
                            </td>
                            <td>
                                <div class="control-group">'. $this->view->formField($this->fields, $key) .'</div>
                            </td>
                        </tr>
                     ';
                  }
               }
            }
         }
         $r .= '</table>';
         return $r . $hidden;
      }
      return '';
   }

}

?>