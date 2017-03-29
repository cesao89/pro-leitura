<?php

/**
 * Class IndexController
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 * @version 1.0
 * @package controller
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
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
        $this->view->title = 'HOME';
        $this->renderScript('mapa/brasil.phtml');
    }
}