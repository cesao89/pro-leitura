<?php

class Zend_View_Helper_Bootstrapnavmenu extends Zend_View_Helper_Abstract {

	var $requestParams;
	var $baseUrl;
	
	var $iconClassActive = "icon-white";
	var $iconClassNotActive = "";

	public function bootstrapnavmenu($bootstrapClassNav, $typeActive, $menu, $defautActive='') {
		// Get controllers necessary info
		$this->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		$this->requestParams = Zend_Controller_Front::getInstance()->getRequest();
		$validator = '';
		
		$res = '<ul class="'.$bootstrapClassNav.'">';
		
		if(!empty($menu) && is_array($menu)){
			foreach($menu as $item){
				if(is_array($item)){
					if($typeActive == 'param'){
						if($item[1] == $defautActive && !$this->requestParams->getParam('ac')){
							$validator = $defautActive;
						}
						elseif($item[1] == $this->requestParams->getParam('ac')){
							$validator = $this->requestParams->getParam('ac');
						}
					} elseif ($typeActive == 'action'){
						if($item[1] == Zend_Controller_Front::getInstance()->getRequest()->getActionName()){
							$validator = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
						}
					}
					
					
					if($item[1] == $validator){
						$res .= '<li class="active"><a href="'.$item[4].'">'.($item[3]!='' ? '<i class="'.$item[3].'"></i> ' : '').$item[0].'</a></li>';
					} else {
						$res .= '<li><a href="'.$item[4].'">'.($item[2]!='' ? '<i class="'.$item[2].'"></i> ' : '').$item[0].'</a></li>';
					}
				}
			}
		}
		
		$res .= '</ul>';


		return $res;
	}



	private function menuPillRenderer($linkName, $href, $iconClass = "") {
		$menu = array(
			 array('novo', 'nv', ''),
			 'departamento' => 'novo'
		);
	}

}

?>