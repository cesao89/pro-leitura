<?php

/**
 * Class MapaController
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 * @version 1.0
 * @package controller
 */
class MapaController extends Zend_Controller_Action
{
    private $session;
    private $auth;

    public function init()
    {
        $this->auth = new Application_Model_Auth();
        $this->session = new Zend_Session_Namespace('proleitura');
        $this->view->usuario = $this->session->usuario;

        if (is_array($this->_helper->flashMessenger->getMessages())){
            foreach ($this->_helper->flashMessenger->getMessages() as $message) {
                $this->_helper->flashMessenger->addMessage($message);
            }
        }

        $_GET = $this->_helper->utils->escape_array($_GET);
        $_POST = $this->_helper->utils->escape_array($_POST);
    }

    public function indexAction()
    {
        $this->_helper->redirector('brasil', 'mapa');
    }

    public function brasilAction()
    {
        $this->view->title = 'Mapa de Projetos';
    }

    public function regiaoSelecionadaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        header('Content-Type: application/json');

        $regiao = $this->getRequest()->getParam('regiao', null);

        if(!$regiao)
            die(false);

        $sqlProjeto = "SELECT `id`, `nome` FROM `proleitura`.`projeto` WHERE `status_id` IN (3,4) AND `localizacao_estado` LIKE '%". strtoupper($regiao) ."%'";
        $fetchProjeto = $this->auth->free_select($sqlProjeto);

        $return = array();
        foreach ($fetchProjeto as $projeto){
            $return[] = array(
                'id' => $projeto->id,
                'nome' => utf8_encode($projeto->nome)
            );
        }

        echo json_encode($return);
        die();
    }
}