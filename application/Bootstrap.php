<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Add databases to the registry
     * 
     * @return void
     */
    public function _initDbRegistry()
    {
        $this->bootstrap('multidb');
        $multidb = $this->getPluginResource('multidb');
        Zend_Registry::set('proleitura', $multidb->getDb('proleitura'));

        // Pega variáveis do application.ini
        $this->config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $this->config);
    }

    /**
     * _initHelpers
     *
     * @desc Sets alternative ways to helpers
     */
    protected function _initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers');
    }

    /**
     *  Inicializa helper brokers
     */
    protected function _initHelperBrokers()
    {
        require_once('Zend/Controller/Action/HelperBroker.php');
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_FlashMessenger());
    }
}
