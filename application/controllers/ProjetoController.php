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

    public function indexAction()
    {
        $list1 = $this->listProjects(array('status_id' => 3));
        $list2 = $this->listProjects(array('status_id' => 4));
        $projects = array_merge($list1, $list2);

        $this->view->title = 'Projetos';
        $this->view->projects = $projects;
    }

    public function meusProjetosAction()
    {
        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        $list1 = $this->listProjects(array('status_id' => 1, 'user_id' => $this->session->usuario['id']));
        $list2 = $this->listProjects(array('status_id' => 2, 'user_id' => $this->session->usuario['id']));
        $list3 = $this->listProjects(array('status_id' => 3, 'user_id' => $this->session->usuario['id']));
        $list4 = $this->listProjects(array('status_id' => 4, 'user_id' => $this->session->usuario['id']));
        $projects = array_merge($list1, $list2, $list3, $list4);

        $this->view->title = 'Projetos';
        $this->view->projects = $projects;
    }

    public function detalhesAction()
    {
        $projectID = $this->getRequest()->getParam('i', null);
        if (!$projectID){
            $this->_helper->FlashMessenger(array('error' => 'Ocorreu um erro para identificar o projeto.'));
            $this->_helper->redirector('index', 'index');
        }

        $projectModel = new Application_Model_Projeto();
        $project = $projectModel->getFullProject($projectID);

        $this->view->title = 'Dados do Projeto - '. $project->nome;
        $this->view->idProject = $projectID;
        $this->view->project = $project;
    }

    public function cadastroAction()
    {
        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $nome = $this->getRequest()->getParam('nome');
            if($nome){
                $sql = "INSERT INTO `proleitura`.`projeto` (`user_id`, `nome`) VALUES ('". $this->session->usuario['id'] ."', '". $nome ."')";
                $this->auth->execute_sql($sql);
                $idProject = $this->auth->get_last_inserted();

                if ($idProject)
                    $this->_helper->redirector('formulario', 'projeto', null, array('i' => $idProject));
            }
        }

        $this->view->title = 'Cadastrar Projeto';
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
        if($project->status_id != 1){
            $this->_helper->FlashMessenger(array('info' => 'Este projeto já foi concluido e não pode ser editado!'));
            $this->_helper->redirector('index', 'projeto');
        }

        $this->view->title = 'Cadastrar Projeto';
        $this->view->idProject = $projectID;
        $this->view->project = $project;
    }

    public function saveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($_SERVER['REQUEST_METHOD'] != 'POST')
            die('requisicao invalida');

        $param = $this->getAllParams();
        $projectID = $param['project_id'];

        # Ajuste dos campos para atualizar os dados
        $fieldsProject = self::ajusteDados($param, $projectID);

        # Salvando o PROJETO
        $projetoModel = new Application_Model_Projeto($projectID);
        if($projetoModel->validate_form($fieldsProject['projeto'])){
            $projetoModel->set_values($fieldsProject['projeto']);
            $projetoModel->save();
        } else {
            foreach ($projetoModel->get_errors() as $error){
                echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
            }
            die;
        }

        # Salvando o PROJETO_CATEGORIAS
        $projetoCategoriasModel = new Application_Model_ProjetoCategorias();
        $projetoCategoriasModel->delByProject($projectID);
        foreach ($fieldsProject['projeto_categorias'] as $row){
            $projetoCategoriasModel = new Application_Model_ProjetoCategorias();
            if($projetoCategoriasModel->validate_form($row)){
                $projetoCategoriasModel->set_values($row);
                $projetoCategoriasModel->save();
            } else {
                foreach ($projetoCategoriasModel->get_errors() as $error){
                    echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
                }
                die;
            }
        }

        # Salvando o PROJETO_DETALHES
        $projetoDetalhesModel = new Application_Model_ProjetoDetalhes($projectID);
        if($projetoDetalhesModel->validate_form($fieldsProject['projeto_detalhes'])){
            $projetoDetalhesModel->set_values($fieldsProject['projeto_detalhes']);
            $projetoDetalhesModel->save();
        } else {
            foreach ($projetoDetalhesModel->get_errors() as $error){
                echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
            }
            die;
        }

        # Salvando o PROJETO_EQUIPE
        $projetoEquipeModel = new Application_Model_ProjetoEquipe();
        $projetoEquipeModel->delByProject($projectID);
        foreach ($fieldsProject['projeto_equipe'] as $row){
            $projetoEquipeModel = new Application_Model_ProjetoEquipe();
            if($projetoEquipeModel->validate_form($row)){
                $projetoEquipeModel->set_values($row);
                $projetoEquipeModel->save();
            } else {
                foreach ($projetoEquipeModel->get_errors() as $error){
                    echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
                }
                die;
            }
        }

        # Salvando o PROJETO_EXPECTATIVA
        $projetoExpectativaModel = new Application_Model_ProjetoExpectativa();
        $projetoExpectativaModel->delByProject($projectID);
        foreach ($fieldsProject['projeto_expectativa'] as $row){
            $projetoExpectativaModel = new Application_Model_ProjetoExpectativa();
            if($projetoExpectativaModel->validate_form($row)){
                $projetoExpectativaModel->set_values($row);
                $projetoExpectativaModel->save();
            } else {
                foreach ($projetoExpectativaModel->get_errors() as $error){
                    echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
                }
                die;
            }
        }

        # Salvando o PROJETO_MAIS_DETALHES
        $projetoMaisDetalhesModel = new Application_Model_ProjetoMaisDetalhes($projectID);
        if($projetoMaisDetalhesModel->validate_form($fieldsProject['projeto_mais_detalhes'])){
            $projetoMaisDetalhesModel->set_values($fieldsProject['projeto_mais_detalhes']);
            $projetoMaisDetalhesModel->save();
        } else {
            foreach ($projetoMaisDetalhesModel->get_errors() as $error){
                echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
            }
            die;
        }

        # Salvando o PROJETO_PARCEIROS
        $projetoParceirosModel = new Application_Model_ProjetoParceiros($projectID);
        if($projetoParceirosModel->validate_form($fieldsProject['projeto_parceiros'])){
            $projetoParceirosModel->set_values($fieldsProject['projeto_parceiros']);
            $projetoParceirosModel->save();
        } else {
            foreach ($projetoParceirosModel->get_errors() as $error){
                echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
            }
            die;
        }

        # Salvando o PROJETO_RESPONSAVEL
        $projetoResponsavelModel = new Application_Model_ProjetoResponsavel($projectID);
        if($projetoResponsavelModel->validate_form($fieldsProject['projeto_responsavel'])){
            $projetoResponsavelModel->set_values($fieldsProject['projeto_responsavel']);
            $projetoResponsavelModel->save();
        } else {
            foreach ($projetoResponsavelModel->get_errors() as $error){
                echo '<div class="alert alert-error">
                    <button data-dismiss="alert" class="close"></button>
                        <b>ATENÇÃO:</b> '. $error['error'] .'
                </div><br>';
            }
            die;
        }

        if($fieldsProject['projeto']['status_id'] == 1){
            sleep(rand(1,5));
            echo '<div id="alert-popup" class="alert alert-info"><button data-dismiss="alert" class="close"></button>Salvo às '. date('d/m/Y H:i:s') .'</div>';
        } else if($fieldsProject['projeto']['status_id'] == 2){
            $this->_helper->FlashMessenger(array('success' => 'Projeto enviado com sucesso!'));
            $this->_helper->redirector('meus-projetos', 'projeto');
        }
    }

    public function excluirAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $projectID = $this->getRequest()->getParam('i', null);

        if(!$projectID){
            $this->_helper->FlashMessenger(array('error' => 'Projeto não encontrado'));
            $this->_helper->redirector('meus-projetos', 'projeto');
        }

        # Salvando o PROJETO
        $projetoModel = new Application_Model_Projeto($projectID);
        $projetoModel->del();

        $this->_helper->FlashMessenger(array('success' => 'Projeto deletado com Sucesso!'));
        $this->_helper->redirector('meus-projetos', 'projeto');
    }

    private function ajusteDados($param, $id = null)
    {
        $projectToSave = array();

        # 1x T:PROJETO
        $projectToSave['projeto'] = array(
            'status_id'                     => (isset($param['status_id']) && !empty($param['status_id'])) ? $param['status_id'] : null,
            'nome'                          => (isset($param['nome']) && !empty($param['nome'])) ? $param['nome'] : null,
            'diferenciais_experiencia'      => (isset($param['diferenciais_experiencia']) && !empty($param['diferenciais_experiencia'])) ? $param['diferenciais_experiencia'] : null,
            'vigencia_inicio'               => (isset($param['iniciado_em']) && !empty($param['iniciado_em'])) ? $this->_helper->utils->dateFormat('01/'.$param['iniciado_em'], 'Y-m-d') : null,
            'vigencia_fim'                  => (isset($param['finalizado_em']) && !empty($param['finalizado_em'])) ? $this->_helper->utils->dateFormat('01/'.$param['finalizado_em'], 'Y-m-d') : null,
            'natureza'                      => null,
            'publico_atendido'              => null,
            'faixa_etaria'                  => (isset($param['faixa_etaria']) && !empty($param['faixa_etaria'])) ? $param['faixa_etaria'] : null,
            'genero'                        => null,
            'atendidos_total'               => (isset($param['atendidos_total']) && !empty($param['atendidos_total'])) ? $param['atendidos_total'] : null,
            'atendidos_ultimo_ano'          => (isset($param['atendidos_ultimo_ano']) && !empty($param['atendidos_ultimo_ano'])) ? $param['atendidos_ultimo_ano'] : null,
            'atendidos_por_acao'            => (isset($param['atendidos_por_acao']) && !empty($param['atendidos_por_acao'])) ? $param['atendidos_por_acao'] : null,
            'atendidos_detalhes'            => (isset($param['atendidos_detalhes']) && !empty($param['atendidos_detalhes'])) ? $param['atendidos_detalhes'] : null,
            'localizacao_territorio'        => null,
            'localizacao_regional'          => null,
            'localizacao_estado'            => null,
            'localizacao_cidade'            => null,
            'localizacao_outro'             => null,
            'organizacao_nome'              => (isset($param['instituicao']) && !empty($param['instituicao'])) ? $param['instituicao'] : null,
        );
        $campos = array(
            'genero'        => 'genero',
            'regional'      => 'localizacao_regional',
            'territorio'    => 'localizacao_territorio',
            'estados'       => 'localizacao_estado',
            'cidades'       => 'localizacao_cidade',
            'natureza'      => 'natureza',
            'publico_alvo'  => 'publico_atendido'
        );
        foreach ($campos as $fieldFrom => $fieldTo){
            if(isset($param[$fieldFrom]) && !empty($param[$fieldFrom])){
                foreach ($param[$fieldFrom] as $val){
                    $projectToSave['projeto'][$fieldTo] .= (!empty($projectToSave['projeto'][$fieldTo])) ? ',' : null;
                    $projectToSave['projeto'][$fieldTo] .= $val;

                    if($fieldFrom == 'natureza' && $val == 'Outra'){
                        $projectToSave['projeto'][$fieldTo] .= ','. $param['natureza_outra_detalhe'];
                    }

                    if($fieldFrom == 'publico_alvo' && $val == 'Outra'){
                            $projectToSave['projeto'][$fieldTo] .= ','. $param['publico_alvo_outra_detalhe'];
                    }
                }
            }
        }

        # 1x T:PROJETO_PARCEIROS
        $projectToSave['projeto_parceiros'] = array(
            'project_id'                => $id,
            'patrocinio'                => (isset($param['parcerias_patrocinio_instituicao']) && !empty($param['parcerias_patrocinio_instituicao'])) ? $param['parcerias_patrocinio_instituicao'] : null,
            'patrocinio_percentual'     => (isset($param['parcerias_patrocinio_porcentagem']) && !empty($param['parcerias_patrocinio_porcentagem'])) ? $param['parcerias_patrocinio_porcentagem'] : null,
            'apoio_tecnico'             => (isset($param['parcerias_apoiotecnico_instituicao']) && !empty($param['parcerias_apoiotecnico_instituicao'])) ? $param['parcerias_apoiotecnico_instituicao'] : null,
            'apoio_institucional'       => (isset($param['parcerias_apoioinstitucional_instituicao']) && !empty($param['parcerias_apoioinstitucional_instituicao'])) ? $param['parcerias_apoioinstitucional_instituicao'] : null,
            'outros'                    => (isset($param['parcerias_outros_instituicao']) && !empty($param['parcerias_outros_instituicao'])) ? $param['parcerias_outros_instituicao'] : null,
        );

        # 1x T:PROJETO_DETALHES
        $projectToSave['projeto_detalhes'] = array(
            'project_id'        => $id,
            'sintese'           => (isset($param['sintese']) && !empty($param['sintese'])) ? $param['sintese'] : null,
            'caracteristicas'   => (isset($param['caracteristicas']) && !empty($param['caracteristicas'])) ? $param['caracteristicas'] : null,
            'objetivos'         => (isset($param['objetivos']) && !empty($param['objetivos'])) ? $param['objetivos'] : null,
            'justificativas'    => (isset($param['justificativas']) && !empty($param['justificativas'])) ? $param['justificativas'] : null,
            'metodologia_a'     => (isset($param['metodologia_a']) && !empty($param['metodologia_a'])) ? $param['metodologia_a'] : null,
            'metodologia_b'     => (isset($param['metodologia_b']) && !empty($param['metodologia_b'])) ? $param['metodologia_b'] : null,
            'resultado'         => (isset($param['resultado']) && !empty($param['resultado'])) ? $param['resultado'] : null,
        );

        # 1x T:PROJETO_MAIS_DETALHES
        $projectToSave['projeto_mais_detalhes'] = array(
            'project_id'                => $id,
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
        $projectToSave['projeto_responsavel'] = array(
            'project_id'                => $id,
            'organizacao'               => (isset($param['organizacao_organizacao']) && !empty($param['organizacao_organizacao'])) ? $param['organizacao_organizacao'] : null,
            'cnpj'                      => (isset($param['organizacao_cnpj']) && !empty($param['organizacao_cnpj'])) ? preg_replace('/\D/', '', $param['organizacao_cnpj']) : null,
            'cidade'                    => (isset($param['organizacao_cidade']) && !empty($param['organizacao_cidade'])) ? $param['organizacao_cidade'] : null,
            'uf'                        => (isset($param['organizacao_estado']) && !empty($param['organizacao_estado'])) ? $param['organizacao_estado'] : null,
            'cep'                       => (isset($param['organizacao_cep']) && !empty($param['organizacao_cep'])) ? preg_replace('/\D/', '', $param['organizacao_cep']) : null,
            'email'                     => (isset($param['organizacao_email']) && !empty($param['organizacao_email'])) ? $param['organizacao_email'] : null,
            'telefone'                  => (isset($param['organizacao_telefone']) && !empty($param['organizacao_telefone'])) ? preg_replace('/\D/', '', $param['organizacao_telefone']) : null,
            'celular'                   => (isset($param['organizacao_celular']) && !empty($param['organizacao_celular'])) ? preg_replace('/\D/', '', $param['organizacao_celular']) : null,
            'site'                      => (isset($param['organizacao_site']) && !empty($param['organizacao_site'])) ? $param['organizacao_site'] : null,
            'facebook'                  => (isset($param['organizacao_facebook']) && !empty($param['organizacao_facebook'])) ? $param['organizacao_facebook'] : null,
            'outros_contatos'           => (isset($param['organizacao_outros_contatos']) && !empty($param['organizacao_outros_contatos'])) ? $param['organizacao_outros_contatos'] : null,
            'pessoa_responsavel'        => (isset($param['pessoa_responsavel']) && !empty($param['pessoa_responsavel'])) ? $param['pessoa_responsavel'] : null,
            'pessoa_cargo'              => (isset($param['pessoa_cargo']) && !empty($param['pessoa_cargo'])) ? $param['pessoa_cargo'] : null,
            'pessoa_email'              => (isset($param['pessoa_email']) && !empty($param['pessoa_email'])) ? $param['pessoa_email'] : null,
            'pessoa_telefone'           => (isset($param['pessoa_telefone']) && !empty($param['pessoa_telefone'])) ? preg_replace('/\D/', '', $param['pessoa_telefone']) : null,
            'pessoa_celular'            => (isset($param['pessoa_celular']) && !empty($param['pessoa_celular'])) ? preg_replace('/\D/', '', $param['pessoa_celular']) : null,
            'pessoa_outros_contatos'    => (isset($param['pessoa_outros']) && !empty($param['pessoa_outros'])) ? $param['pessoa_outros'] : null,
        );

        # +1x T:PROJETO_CATEGORIAS
        $projectToSave['projeto_categorias'] = array();
        if(isset($param['categoria']) && !empty($param['categoria'])){
            foreach ($param['categoria'] as $categoria){
                switch ($categoria){
                    case 'Órgão público': $prefix = 'orgao'; break;
                    case 'Outra': $prefix = 'outra'; break;
                    default: $prefix = null;
                }

                $projectToSave['projeto_categorias'][] = array(
                    'project_id'    => $id,
                    'categoria'     => $categoria,
                    'detalhe'       => (isset($param['categoria_'. $prefix .'_detalhe']) && !empty($param['categoria_'. $prefix .'_detalhe'])) ? $param['categoria_'. $prefix .'_detalhe'] : null,
                );
            }
        }

        # +1x T:PROJETO_EQUIPE
        $listEquipe = array('equipe_coordenador', 'equipe_professor', 'equipe_educador', 'equipe_bibliotecario', 'equipe_voluntario', 'equipe_mediador', 'equipe_outros');
        $projectToSave['projeto_equipe'] = array();
        foreach ($listEquipe as $fieldEquipe){
            if(isset($param[$fieldEquipe]) && !empty($param[$fieldEquipe])){
                $projectToSave['projeto_equipe'][] = array(
                    'project_id' => $id,
                    'quantidade' => $param[$fieldEquipe],
                    'equipe'     => $fieldEquipe,
                    'detalhe'    => ($fieldEquipe == 'equipe_outros') ? (isset($param['equipe_outros_detalhe']) && !empty($param['equipe_outros_detalhe'])) ? $param['equipe_outros_detalhe'] : null : null,
                );
            }
        }

        # +1x T:PROJETO_EXPECTATIVA
        $projectToSave['projeto_expectativa'] = array();
        if(isset($param['expectativas']) && !empty($param['expectativas'])){
            foreach ($param['expectativas'] as $expectativa){
                $projectToSave['projeto_expectativa'][] = array(
                    'project_id'  => $id,
                    'expectativa' => $expectativa,
                    'detalhe'     => ($expectativa == 'Outra') ? (isset($param['expectativas_outra']) && !empty($param['expectativas_outra'])) ? $param['expectativas_outra'] : null : null,
                );
            }
        }

        return $projectToSave;
    }

    private function listProjects($where, $limit=500)
    {
        $projetoModel = new Application_Model_Projeto();
        $projetoFetch = $projetoModel->where($where)->limit(0,$limit)->order_by('-id')->filter();

        $projects = array();
        foreach ($projetoFetch as $project){
            $projetoStatusModel = new Application_Model_ProjetoStatus($project->status_id);
            $projects[] = array(
                'id'                => (isset($project->id) && !empty($project->id)) ? $project->id : null,
                'user_id'           => (isset($project->user_id) && !empty($project->user_id)) ? $project->user_id : null,
                'nome'              => (isset($project->nome) && !empty($project->nome)) ? $project->nome : null,
                'territorio'        => (isset($project->localizacao_territorio) && !empty($project->localizacao_territorio)) ? $this->_helper->utils->fullNameCountry($project->localizacao_territorio, ',') : null,
                'regional'          => (isset($project->localizacao_regional) && !empty($project->localizacao_regional)) ? $project->localizacao_regional : null,
                'estado'            => (isset($project->localizacao_estado) && !empty($project->localizacao_estado)) ? $project->localizacao_estado : null,
                'cidade'            => (isset($project->localizacao_cidade) && !empty($project->localizacao_cidade)) ? $project->localizacao_cidade : null,
                'vigencia_inicio'   => (isset($project->vigencia_inicio) && !empty($project->vigencia_inicio)) ? $this->_helper->utils->dateFormat($project->vigencia_inicio, 'm/Y') : null,
                'vigencia_fim'      => (isset($project->vigencia_fim) && !empty($project->vigencia_fim)) ? ($project->vigencia_fim != '0000-00-00') ? $this->_helper->utils->dateFormat($project->vigencia_fim, 'm/Y') : null : null,
                'status'            => $projetoStatusModel->status,
            );
        }

        return $projects;
    }
}