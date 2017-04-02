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

    public function internacionalAction()
    {
        $projetoModel = new Application_Model_Projeto();
        $list1 = $projetoModel->listProject(array('status_id' => 3), 100);
        $list2 = $projetoModel->listProject(array('status_id' => 4), 100);
        $projects = array_merge($list1, $list2);

        $projetosFetch = array();
        foreach ($projects as $project) {
            $project['territorio'] = explode(',', $project['territorio']);
            $project['estado'] = explode(',', $project['estado']);
            $project['cidade'] = explode(',', $project['cidade']);

            $location = null;
            if($project['territorio']){
                foreach ($project['territorio'] as $pais){
                    if($pais == 'BRA')
                        continue;
                    $location[] = $this->_helper->utils->fullNameCountry($pais);
                }

                if($project['estado']){
                    foreach ($project['estado'] as $estado) {
                        if($project['cidade']){
                            foreach ($project['cidade'] as $cidade) {
                                $location[] = $cidade .', '. $estado .', Brasil';
                            }
                        } else {
                            $location[] = $estado .', Brasil';
                        }
                    }
                }
            }

            foreach ($location as $val){
                $url = 'http://maps.googleapis.com/maps/api/geocode/json?address='. urlencode($val) .'&sensor=false';

                $cURL = curl_init($url);
                curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
                $resultado = curl_exec($cURL);

                $resposta = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
                curl_close($cURL);

                if ($resposta != '200') {
                    continue;
                } else {
                    $return = json_decode($resultado);
                }

                if(!isset($return->results[0]->geometry->location) || empty($return->results[0]->geometry->location))
                    continue;

                $projetosFetch[] = array(
                    'id'    => $project['id'],
                    'nome'  => $project['nome'],
                    'lat'   => $return->results[0]->geometry->location->lat,
                    'lng'   => $return->results[0]->geometry->location->lng,
                );
            }
        }

        $this->view->title = 'Mapa de Projetos';
        $this->view->projects = $projetosFetch;
    }

    public function embedBrasilAction()
    {
        $this->_helper->layout->setLayout('embed');
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