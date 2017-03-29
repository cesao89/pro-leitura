<?php

/**
 * Helper Bootstrap table generator
 * 
 * @example 
 * <code>
  $thContent = array(
  'name' => 'Nome',
  'age' => 'Idade',
  'gender' => 'Sexo'
  );

  $trContent = array(
  array(
  'name' => 'José Roberto',
  'age' => 23,
  'gender' => 'Masculino'
  ),
  array(
  'name' => 'Regina Frani',
  'age' => 34,
  'gender' => 'Feminino'
  ),
  array(
  'name' => 'Tarciso Moreno',
  'age' => 16,
  'gender' => 'Masculino'
  ),
  array(
  'name' => 'Roberta Maria',
  'age' => 45,
  'gender' => 'Feminino'
  )
  );

  $actions = array(
 *
 * 0 - Button label
 * 1 - class button
 * 2 - url button
 * 3 - icon button
 *
  array('', 'btn', $this->baseUrl . '/sistema/departamento/ac/editar/i/%name%', 'icon-edit'),
  array('', 'btn', $this->baseUrl . '/sistema/departamento/ac/remover/i/%name%', 'icon-remove')
  );

  $rowRules = array(
 *
 * 0 - Key TH content
 * 1 - Compatator
 * 2 - Integer or string to compare with TH content
 * 3 - Class to apply if true
 *
  array('age', '<', 30, 'warning'),
  array('age', '>', 40, 'error'),
  array('name', '==', 'José Roberto', 'info')
  );

  $description = "Some string description to apear on top the table"
 * 
 * $filter = array(
  array('currency', 'field_name', array('display' => Zend_Currency::NO_SYMBOL)), // [0] = filter [1] field [2] Zend_Currency setFormat() array
  array('prepend', 'field_name', 'R$'), array('append', 'totalImagem', '!'),
  array('attribute', 'field_name', 'class', 'centralizar'),
  array('replace', 'field_name', array('1' => 'Sim', '0' => 'Não'))
  array('datetime', 'creationDate', array('format' => "dd/MM/YYYY HH:mm")), // [0] = filter [1] field [2] attibutes (optional)
  array('datetime', 'creationDate'), // [0] = filter [1] field - [2] is optional, default format "dd/MM/YYYY HH:mm"
  array('date', 'creationDate'), // same as datetime
  )
 * </code>
 */
class Zend_View_Helper_Bootstraptable extends Zend_View_Helper_Abstract
{

   var $requestParams;
   var $baseUrl;
   var $actionLabel = 'Ação';

   /**
    * 
    * 
    * @param string $bootstrapClassTable Class name from Bootstrap to use
    * @param array $thContent TH titles for column description
    * @param array $trContent TR content list
    * @param array $actions (optional) Actions for each row
    * @param array $rowRules (optional) Rules comparator to add class on TR
    * @param string $description (optional) Description for table
    * @return string Table generated
    */
   public function bootstraptable($bootstrapClassTable, $thContent, $trContent, $actions = '', $rowRules = '', $description = '', $filter = '') {
      // Get controllers necessary info
      $this->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $this->requestParams = Zend_Controller_Front::getInstance()->getRequest();

      $th = '';
      $tr = '';
      $aditionalAttributes = '';

      $res = '<table class="' . $bootstrapClassTable . '">';

      if (!empty($thContent) && is_array($thContent) && !empty($trContent) && is_array($trContent)) {
         // HEADER
         $th .= "<tr>\r\n";
         foreach ($thContent as $key => $column) {
            $th .= "<th id=\"" . $key . "\" style=\"text-align:center !important\">" . $column . "</th>\r\n";
         }
         if (is_array($actions) && !empty($actions)) {
            $th .= "<th id=\"action\" style=\"text-align:center !important\">" . $this->actionLabel . "</th>";
         }
         $th .= "</tr>\r\n";

         // CONTENT
         foreach ($trContent as $trColumnItem) {
            $tr .= "<tr " . $this->applyRowRuleClass($trColumnItem, $rowRules) . ">\r\n";
            foreach ($thContent as $keyTarget => $column) {
               if (is_array($trColumnItem)) {
                  $tr .= "\t<td" . $this->tagApplyFilter($filter, $trColumnItem) . ">" . $this->getTr($keyTarget, $trColumnItem, $filter) . "</td>";
               }
            }

            if (is_array($actions) && !empty($actions)) {
               $tr .= "<td style=\"text-align:center; vertical-align: middle;\">";
               foreach ($actions as $act) {
                  if (is_array($act)){
                     if ($act[3] != "") {
                        $labelButton = '<i class="' . $act[3] . '"></i>' . ($act[0] != '' ? ' ' . $act[0] : '');
                     } else {
                        $labelButton = $act[0];
                     }

                     if (isset($act[4])) {
                        $aditionalAttributes = $this->getAttibutes($act[4], $trColumnItem);
                     }

                     $tr .= '<button class="' . $act[1] . '" type="button" onclick="location.href=\'' . htmlspecialchars($this->convertProperties($act[2], $trColumnItem)) . '\'" ' . $aditionalAttributes . '>' . $labelButton . '</button> ';
                  }
               }
               $tr .= "</td>";
            }
            $tr .= " <tr>\r\n";
         }
      }

      $res .= $th . $tr . '</table>';


      return $res;
   }

   private function getTr($targetColumn, $trColumnItem, $filter) {
      foreach ($trColumnItem as $key => $item) {
         if ($key == $targetColumn) {
            return $this->valueApplyFilter($filter, $key, $item);
         }
      }
      return '';
   }

   /**
    * Convert meta names %??% 
    * 
    * @param String $stringIn Input string to substitute meta
    * @param Array $columnData Array with data column
    * @param type $divisor (Optional) Divisor
    * @return String Input replaced
    */
   private function convertProperties($stringIn, $columnData, $divisor = '%') {
      $stringOut = '';

      if (strripos($stringIn, $divisor)) {
         $str = explode($divisor, $stringIn);
         for ($a = 0; $a < count($str); $a++) {
            if ($a % 2 == 0) {
               $stringOut .= $str[$a];
            } else {
               $data = (isset($columnData[$str[$a]]) ? $columnData[$str[$a]] : 'unknownColumnInConstructor');
               $stringOut .= $data;
            }
         };
         return $stringOut;
      }
      return $stringIn;
   }

   private function applyRowRuleClass($trContent, $rowRules) {
      $res = '';
      if (is_array($rowRules)) {
         foreach ($trContent as $key => $value) {
            foreach ($rowRules as $rule) {
               if (isset($rule[0]) && isset($rule[1]) && isset($rule[2]) && isset($rule[3])) {
                  if ($key == $rule[0]) {
                     if (is_integer($rule[2]) || is_double($rule[2])) {
                        eval('$res = (' . $value . $rule[1] . $rule[2] . ' ? " class=\"' . $rule[3] . '\" " : "");');
                     } else {
                        eval('$res = ("' . $value . '"' . $rule[1] . '"' . $rule[2] . '"' . ' ? " class=\"' . $rule[3] . '\" " : "");');
                     }

                     if ($res != "") {
                        return $res;
                     }
                  }
               }
            }
         }
      }
      return $res;
   }

   private function getAttibutes($attibutes, $columnData) {
      $result = '';

      if (is_null($attibutes) || $attibutes == '') {
         return $result;
      }

      if (!is_array($attibutes)) {
         throw new Exception("Estrutura de atributos não é um array");
      }

      foreach ($attibutes as $key => $value) {
         $result .= $key . '="' . $this->convertProperties($value, $columnData) . '" ';
      }

      return $result;
   }

   private function tagApplyFilter($filter, $trColumnItem) {
      $res = '';

      if ($filter == '' || empty($filter)) {
         return "";
      }

      if (!is_array($filter)) {
         throw new Exception("Estrutura de atributos de filtro não é um array");
      }

      foreach ($filter as $applyFilter) {
         if (!is_array($applyFilter)) {
            throw new Exception("Estrutura de filtro não é um array");
         }

         if (!empty($applyFilter)) {
            if (strtolower($applyFilter[0]) == 'attribute') {
               foreach ($trColumnItem as $key => $value) {
                  if ($applyFilter[1] == $key) {
                     $res .= " " . $this->getAttibutes(array($applyFilter[2] => $applyFilter[3]), array($key => $value));
                  }
               }
            }
         }
      }

      return $res;
   }

   private function valueApplyFilter($filter, $field, $value) {
      $res = $value;

      if (empty($filter) || $filter == '') {
         return $res;
      }

      if (!is_array($filter)) {
         throw new Exception("Estrutura de atributos de filtro não é um array");
      }

      foreach ($filter as $applyFilter) {
         if (!is_array($applyFilter)) {
            throw new Exception("Estrutura de filtro não é um array");
         }

         if (!empty($applyFilter)) {
            if (strtolower($applyFilter[0]) == 'prepend' && $field == $applyFilter[1]) {
               $res = $applyFilter[2] . $res;
            }
            if (strtolower($applyFilter[0]) == 'append' && $field == $applyFilter[1]) {
               $res = $res . $applyFilter[2];
            }
            if (strtolower($applyFilter[0]) == 'replace' && $field == $applyFilter[1]) {
               if (is_array($applyFilter[2])) {
                  foreach ($applyFilter[2] as $key => $value) {
                     if (strval($res) == strval($key)){
                       $res = $applyFilter[2][$key]; 
                     }
                     //$res = str_replace($key, $value, $res);
                  }
               }
            }
            if (strtolower($applyFilter[0]) == 'currency' && $field == $applyFilter[1]) {
               if (is_int(intval($value)) || is_double(doubleval($value)) || is_float(floatval($value))) {
                  $currency = new Zend_Currency();

                  if (isset($applyFilter[2]) && is_array($applyFilter[2])) {
                     $currency->setFormat($applyFilter[2]);
                  }

                  $res = $currency->toCurrency($value);
               }
            }
            if (strtolower($applyFilter[0]) == 'datetime' && $field == $applyFilter[1]) {
               $format = "dd/MM/YYYY HH:mm";
               
               if(isset($applyFilter[2]['format']) && isset($applyFilter[2])){
                  $format = $applyFilter[2]['format'];
               }
               
               if ($value && $value!='') {
                  $ini = new Zend_Date($value);
                  $res = $ini->get($format);
               }
            }
            if (strtolower($applyFilter[0]) == 'date' && $field == $applyFilter[1]) {
               $format = "dd/MM/YYYY";
               
               if(isset($applyFilter[2]['format']) && isset($applyFilter[2])){
                  $format = $applyFilter[2]['format'];
               }
               
               if ($value && $value!='') {
                  $ini = new Zend_Date($value);
                  $res = $ini->get($format);
               }
            }
         }
      }

      if ($res == '') {
         $res = $value;
      }

      return $res;
   }

}

?>