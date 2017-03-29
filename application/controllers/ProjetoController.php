<?php

/**
 * Controller Projeto
 * 
 * @author Cesar O Domingos <cesar_web@live.com>
 * @version 1.0
 * @package controller
 */
class ProjetoController extends Zend_Controller_Action
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

    // TODO: Exibir os projetos publicos (status IN (...briefing...))
    public function indexAction()
    {
    }

    public function cadastroAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        $sql = "INSERT INTO `proleitura`.`projeto` (`user_id`) VALUES ('". $this->session->usuario['id'] ."')";
        $this->auth->execute_sql($sql);
        $idProject = $this->auth->get_last_inserted();

        if ($idProject)
            $this->_helper->redirector('formulario', 'projeto', null, array('i' => $idProject));

        die('Ops, algo deu errado');
    }

    public function formularioAction()
    {
        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        $projectID = $this->getRequest()->getParam('i', null);
        if (!$projectID){
            $this->_helper->FlashMessenger(array('error' => '[406] Ocorreu um erro para identificar o projeto.'));
            $this->_helper->redirector('perfil', 'usuario');
        }

        $projectModel = new Application_Model_Projeto();
        $project = $projectModel->getFullProject($projectID);

        $this->view->title = 'Cadastrar Projeto';
        $this->view->idProject = $projectID;
        $this->view->project = $project;
    }

    // TODO: Finalizar....
    public function saveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($_SERVER['REQUEST_METHOD'] != 'POST')
            die('requisicao invalida');

        $param = $this->getAllParams();
        $projectID = $param['project_id'];

        # 1x T:PROJETO
        $toSaveProjeto = array(
            'status_id'                     => (isset($param['status']) && !empty($param['status'])) ? $param['status'] : null,
            'nome'                          => (isset($param['nome']) && !empty($param['nome'])) ? $param['nome'] : null,
            'diferenciais_experiencia'      => (isset($param['diferenciais_experiencia']) && !empty($param['diferenciais_experiencia'])) ? $param['diferenciais_experiencia'] : null,
            'vigencia_inicio'               => (isset($param['iniciado_em']) && !empty($param['iniciado_em'])) ? $this->_helper->utils->dateFormat('01/'.$param['iniciado_em'], 'Y-m-d') : null,
            'vigencia_fim'                  => (isset($param['finalizado_em']) && !empty($param['finalizado_em'])) ? $this->_helper->utils->dateFormat('01/'.$param['finalizado_em'], 'Y-m-d') : null,
            'natureza'                      => (isset($param['natureza']) && !empty($param['natureza'])) ? ($param['natureza'] == 'Outra') ? $param['natureza_outra_detalhe'] : $param['natureza'] : null,
            'publico_atendido'              => (isset($param['publico_alvo']) && !empty($param['publico_alvo'])) ? ($param['publico_alvo'] == 'Outra') ? $param['publico_alvo_outra_detalhe'] : $param['publico_alvo'] : null,
            'faixa_etaria'                  => (isset($param['faixa_etaria']) && !empty($param['faixa_etaria'])) ? $param['faixa_etaria'] : null,
            'genero'                        => null,
            'atendidos_total'               => (isset($param['atendidos_total']) && !empty($param['atendidos_total'])) ? $param['atendidos_total'] : null,
            'atendidos_ultimo_ano'          => (isset($param['atendidos_ultimo_ano']) && !empty($param['atendidos_ultimo_ano'])) ? $param['atendidos_ultimo_ano'] : null,
            'atendidos_por_acao'            => (isset($param['atendidos_por_acao']) && !empty($param['atendidos_por_acao'])) ? $param['atendidos_por_acao'] : null,
            'atendidos_detalhes'            => (isset($param['atendidos_detalhes']) && !empty($param['atendidos_detalhes'])) ? $param['atendidos_detalhes'] : null,
            'localizacao_territorio'        => (isset($param['territorio']) && !empty($param['territorio'])) ? $param['territorio'] : null,
            'localizacao_regional'          => null,
            'localizacao_estado'            => null,
            'localizacao_cidade'            => null,
            'localizacao_outro'             => null,
            'organizacao_nome'              => (isset($param['instituicao']) && !empty($param['instituicao'])) ? $param['instituicao'] : null,
        );
        $campos = array('genero' => 'genero', 'regional' => 'localizacao_regional', 'estados' => 'localizacao_estado', 'cidades' => 'localizacao_cidade');
        foreach ($campos as $fieldFrom => $fieldTo){
            if(isset($param[$fieldFrom]) && !empty($param[$fieldFrom])){
                foreach ($param[$fieldFrom] as $val){
                    if(!empty($toSaveProjeto[$fieldTo]))
                        $toSaveProjeto[$fieldTo] .= ',';
                    $toSaveProjeto[$fieldTo] .= $val;
                }
            }
        }

        # +1x T:ORGANIZACAO_CATEGORIA
        $toSaveOrganizacaoCategoria = array();
        if(isset($param['categoria']) && !empty($param['categoria'])){
            foreach ($param['categoria'] as $categoria){
                switch ($categoria){
                    case 'Órgão público': $prefix = 'orgao'; break;
                    case 'Outra': $prefix = 'outra'; break;
                    default: $prefix = null;
                }

                $toSaveOrganizacaoCategoria[] = array(
                    'categoria' => $categoria,
                    'detalhe'   => (isset($param['categoria_'. $prefix .'_detalhe']) && !empty($param['categoria_'. $prefix .'_detalhe'])) ? $param['categoria_'. $prefix .'_detalhe'] : null,
                );
            }
        }

        # 1x T:ORGANIZACAO_PARCEIROS
        $toSaveOrganizacaoParceiros = array(
            'patrocinio'                => (isset($param['parcerias_patrocinio_instituicao']) && !empty($param['parcerias_patrocinio_instituicao'])) ? $param['parcerias_patrocinio_instituicao'] : null,
            'patrocinio_percentual'     => (isset($param['parcerias_patrocinio_porcentagem']) && !empty($param['parcerias_patrocinio_porcentagem'])) ? $param['parcerias_patrocinio_porcentagem'] : null,
            'apoio_tecnico'             => (isset($param['parcerias_apoiotecnico_instituicao']) && !empty($param['parcerias_apoiotecnico_instituicao'])) ? $param['parcerias_apoiotecnico_instituicao'] : null,
            'apoio_institucional'       => (isset($param['parcerias_apoioinstitucional_instituicao']) && !empty($param['parcerias_apoioinstitucional_instituicao'])) ? $param['parcerias_apoioinstitucional_instituicao'] : null,
            'outros'                    => (isset($param['parcerias_outros_instituicao']) && !empty($param['parcerias_outros_instituicao'])) ? $param['parcerias_outros_instituicao'] : null,
        );

        # 1x T:PROJETO_DETALHES
        $toSaveProjetoDetalhes = array(
            'sintese'           => (isset($param['sintese']) && !empty($param['sintese'])) ? $param['sintese'] : null,
            'caracteristicas'   => (isset($param['caracteristicas']) && !empty($param['caracteristicas'])) ? $param['caracteristicas'] : null,
            'objetivos'         => (isset($param['objetivos']) && !empty($param['objetivos'])) ? $param['objetivos'] : null,
            'justificativas'    => (isset($param['justificativas']) && !empty($param['justificativas'])) ? $param['justificativas'] : null,
            'metodologia_a'     => (isset($param['metodologia_a']) && !empty($param['metodologia_a'])) ? $param['metodologia_a'] : null,
            'metodologia_b'     => (isset($param['metodologia_b']) && !empty($param['metodologia_b'])) ? $param['metodologia_b'] : null,
            'resultado'         => (isset($param['resultado']) && !empty($param['resultado'])) ? $param['resultado'] : null,
        );

        # +1x T:PROJETO_EQUIPE
        $listEquipe = array('equipe_coordenador', 'equipe_professor', 'equipe_educador', 'equipe_bibliotecario', 'equipe_voluntario', 'equipe_mediador', 'equipe_outros');
        $toSaveProjetoEquipe = array();
        foreach ($listEquipe as $fieldEquipe){
            if(isset($param[$fieldEquipe]) && !empty($param[$fieldEquipe])){
                $toSaveProjetoEquipe[] = array(
                    'quantidade' => $param[$fieldEquipe],
                    'equipe'     => $fieldEquipe,
                    'detalhe'    => ($fieldEquipe == 'equipe_outros') ? (isset($param['equipe_outros_detalhe']) && !empty($param['equipe_outros_detalhe'])) ? $param['equipe_outros_detalhe'] : null : null,
                );
            }
        }

        # +1x T:PROJETO_EXPECTATIVA
        $toSaveProjetoExpectativa = array();
        if(isset($param['expectativas']) && !empty($param['expectativas'])){
            foreach ($param['expectativas'] as $expectativa){
                $toSaveProjetoExpectativa[] = array(
                    'expectativa' => $expectativa,
                    'detalhe'     => ($expectativa == 'Outra') ? (isset($param['expectativas_outra']) && !empty($param['expectativas_outra'])) ? $param['expectativas_outra'] : null : null,
                );
            }
        }

        # 1x T:PROJETO_MAIS_DETALHES
        $toSaveProjetoMaisDetalhes = array(
            'avaliacoes'                => (isset($param['avaliacoes']) && !empty($param['avaliacoes'])) ? $param['avaliacoes'] : null,
            'depoimentos'               => (isset($param['depoimentos']) && !empty($param['depoimentos'])) ? $param['depoimentos'] : null,
            'premios'                   => (isset($param['premios']) && !empty($param['premios'])) ? $param['premios'] : null,
            'principais_dificuldades'   => (isset($param['principais_dificuldades']) && !empty($param['principais_dificuldades'])) ? $param['principais_dificuldades'] : null,
            'dificuldades_superadas'    => (isset($param['dificuldades_superadas']) && !empty($param['dificuldades_superadas'])) ? $param['dificuldades_superadas'] : null,
            'garantir_continuidade'     => (isset($param['garantir_continuidade']) && !empty($param['garantir_continuidade'])) ? $param['garantir_continuidade'] : null,
            'site'                      => (isset($param['site']) && !empty($param['site'])) ? $param['site'] : null,
            'redes_sociais'             => (isset($param['redes_sociais']) && !empty($param['redes_sociais'])) ? $param['redes_sociais'] : null,
            'fotos_videos'              => (isset($param['fotos_videos']) && !empty($param['fotos_videos'])) ? $param['fotos_videos'] : null,
            'adicional'                 => (isset($param['adicional']) && !empty($param['adicional'])) ? $param['adicional'] : null,
        );

        # 1x T:PROJETO_RESPONSAVEL
        $toSaveProjetoResponsavel = array(
            'organizacao'               => (isset($param['organizacao_organizacao']) && !empty($param['organizacao_organizacao'])) ? $param['organizacao_organizacao'] : null,
            'cnpj'                      => (isset($param['organizacao_cnpj']) && !empty($param['organizacao_cnpj'])) ? $param['organizacao_cnpj'] : null,
            'cidade'                    => (isset($param['organizacao_cidade']) && !empty($param['organizacao_cidade'])) ? $param['organizacao_cidade'] : null,
            'uf'                        => (isset($param['organizacao_estado']) && !empty($param['organizacao_estado'])) ? $param['organizacao_estado'] : null,
            'cep'                       => (isset($param['organizacao_cep']) && !empty($param['organizacao_cep'])) ? $param['organizacao_cep'] : null,
            'email'                     => (isset($param['organizacao_email']) && !empty($param['organizacao_email'])) ? $param['organizacao_email'] : null,
            'telefone'                  => (isset($param['organizacao_telefone']) && !empty($param['organizacao_telefone'])) ? $param['organizacao_telefone'] : null,
            'celular'                   => (isset($param['organizacao_celular']) && !empty($param['organizacao_celular'])) ? $param['organizacao_celular'] : null,
            'site'                      => (isset($param['organizacao_site']) && !empty($param['organizacao_site'])) ? $param['organizacao_site'] : null,
            'facebook'                  => (isset($param['organizacao_facebook']) && !empty($param['organizacao_facebook'])) ? $param['organizacao_facebook'] : null,
            'outros_contatos'           => (isset($param['organizacao_outros_contatos']) && !empty($param['organizacao_outros_contatos'])) ? $param['organizacao_outros_contatos'] : null,
            'pessoa_responsavel'        => (isset($param['pessoa_responsavel']) && !empty($param['pessoa_responsavel'])) ? $param['pessoa_responsavel'] : null,
            'pessoa_cargo'              => (isset($param['pessoa_cargo']) && !empty($param['pessoa_cargo'])) ? $param['pessoa_cargo'] : null,
            'pessoa_email'              => (isset($param['pessoa_email']) && !empty($param['pessoa_email'])) ? $param['pessoa_email'] : null,
            'pessoa_telefone'           => (isset($param['pessoa_telefone']) && !empty($param['pessoa_telefone'])) ? $param['pessoa_telefone'] : null,
            'pessoa_celular'            => (isset($param['pessoa_celular']) && !empty($param['pessoa_celular'])) ? $param['pessoa_celular'] : null,
            'pessoa_outros_contatos'    => (isset($param['pessoa_outros']) && !empty($param['pessoa_outros'])) ? $param['pessoa_outros'] : null,
        );


        // TODO: SALVAR DADOS
        print('<pre>');
        print_r($toSaveProjetoResponsavel);
        die;


        $usuario = new Application_Model_Usuario();
        if(isset($param['i']) && !empty($param['i'])){
            unset($toSave['email']);
            unset($toSave['password']);
            $usuario->get($param['i']);
        }

        if ($usuario->validate_form($toSave)) {
            if ($usuario->save()) {
                unset($this->session->form['user']);

                if(isset($param['i']) && !empty($param['i'])){
                    $this->session->usuario['nome'] = $toSave['name'];
                    $this->session->usuario['telefone'] = $toSave['phone'];
                    $this->session->usuario['documento'] = $toSave['num_document'];
                }

                $this->_helper->FlashMessenger(array('success' => 'Usuário cadastrado com sucesso.'));
                $this->_helper->redirector('login', 'usuario');
            } else {
                $this->_helper->FlashMessenger(array('error' => '[409] Ocorreu um erro para cadastrar o usuário.'));
            }
        } else {
            $this->_helper->FlashMessenger(array('error' => '[406] Dados inválido.'));
        }

        $this->_helper->redirector('cadastro', 'usuario');
    }

    // TODO: Criar página de detalhes do projeto.
    public function detalhesAction()
    {
        $projectID = $this->getRequest()->getParam('i', null);
        if (!$projectID){
            $this->_helper->FlashMessenger(array('error' => '[406] Ocorreu um erro para identificar o projeto.'));
            $this->_helper->redirector('index', 'index');
        }

        $projectModel = new Application_Model_Projeto();
        $project = $projectModel->getFullProject($projectID);

        $this->view->title = 'Dados do Projeto - '. $project->nome;
        $this->view->idProject = $projectID;
        $this->view->project = $project;
    }
}