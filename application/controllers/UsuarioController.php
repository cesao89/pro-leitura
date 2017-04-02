<?php

/**
 * Controller Usuario
 * 
 * @author Cesar O Domingos <cesar_web@live.com>
 * @version 1.0
 * @package controller
 */
class UsuarioController extends Zend_Controller_Action
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
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->redirector('login', 'usuario');
    }

    public function loginAction()
    {
        if ($this->auth->is_logged($this->session))
            $this->_helper->redirector('perfil', 'usuario');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = $this->getRequest()->getPost('usuario', null);
            $senha = $this->getRequest()->getPost('senha', null);

            if ($usuario && $senha) {
                $authModel = new Application_Model_Auth();
                $auth = $authModel->autenticar($usuario, $senha);

                if ($auth) {
                    $this->session->logado = true;
                    $this->session->usuario = array(
                        'id'        => $auth['id'],
                        'nome'      => $auth['name'],
                        'profile'   => $auth['profile'],
                        'email'     => $auth['email'],
                        'telefone'  => $auth['phone'],
                        'documento' => $auth['num_document']
                    );

                    $this->_helper->redirector('perfil', 'usuario');
                } else {
                    $this->_helper->FlashMessenger(array('error' => $authModel->msg));
                }
            } else {
                $this->_helper->FlashMessenger(array('error' => 'Usuário e/ou senha inválido.'));
            }
        }

        $this->view->title = 'Log In';
    }

    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->session->logado = false;
        $this->session->usuario = false;
        unset($this->session->logado);
        unset($this->session->usuario);

        $this->_helper->redirector('index', 'index');
    }

    public function perfilAction()
    {
        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('index', 'usuario');

        $param = ($this->session->usuario['profile'] == 'gestor') ? $this->getAllParams() : null;
        $id = (isset($param['i']) && !empty($param['i'])) ? $param['i'] : $this->session->usuario['id'];

        if (!$id) {
            $this->_helper->FlashMessenger(array('error' => 'Usuário não encontrado.'));
            $this->_helper->redirector('index', 'index');
        }

        $usuario = new Application_Model_Usuario($id);
        $projeto = new Application_Model_Projeto();

        $usuarioView = array(
            'id'                => $usuario->id,
            'nome'              => $usuario->name,
            'email'             => $usuario->email,
            'telefone'          => $this->_helper->utils->mask($usuario->phone, (strlen($usuario->phone) > 10) ? '(##) #####-####' : '(##) ####-####'),
            'cpf'               => $this->_helper->utils->mask($usuario->num_document, '###.###.###-##'),
            'status'            => $usuario->status,
            'cadastrado'        => $this->_helper->utils->dateFormat($usuario->created_at, 'd/m/Y'),
            'atualizado'        => $this->_helper->utils->dateFormat($usuario->updated_at, 'd/m/Y H:i:s'),
            'ultimos_projetos'  => $projeto->lastProjects($id),
        );

        $this->view->title = 'Perfil';
        $this->view->profile = $usuarioView;
    }

    public function cadastroAction()
    {
        // Verifica se esta logado
        if ($this->auth->is_logged($this->session) && $this->session->usuario['profile'] != 'gestor') {
            $this->_helper->redirector('perfil', 'usuario');
        }

        $usuario = new Application_Model_Usuario();

        if ($this->session->usuario['profile'] != 'gestor')
            $usuario->fields['profile_id']['form'] = false;

        if(isset($this->session->form['user']) && !empty($this->session->form['user']))
            $usuario->set_values($this->session->form['user']);

        // Seta variáveis na View
        $this->view->title = "Cadastrar Usuário";
        $this->view->form_fields = $usuario->fields;
    }

    public function editarAction()
    {
        if(!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        $param = ($this->session->usuario['profile'] == 'gestor') ? $this->getAllParams() : null;
        $id = (isset($param['i']) && !empty($param['i'])) ? $param['i'] : $this->session->usuario['id'];

        if (!$id) {
            $this->_helper->FlashMessenger(array('error' => 'Usuário não encontrado.'));
            $this->_helper->redirector('perfil', 'usuario');
        }

        $usuario = new Application_Model_Usuario($id);

        $dadosUsuario = array();
        foreach ($usuario->fields as $field => $value) {
            $dadosUsuario[$field] = $value['value'];
        }

        // Seta variáveis na View
        $this->view->title = "Editar Usuário";
        $this->view->dados = $dadosUsuario;
        $this->view->id = $id;
    }

    public function saveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->_helper->FlashMessenger(array('error' => '[401] Ocorreu um erro para cadastrar o usuário.'));
        }

        $param = $this->getAllParams();

        $toSave = array(
            'profile_id'    => (isset($param['profile_id']) && !empty($param['profile_id'])) ? $param['profile_id'] : 2,
            'name'          => $param['name'],
            'email'         => (isset($param['email']) && !empty($param['email'])),
            'phone'         => preg_replace('/\D/', '', $param['phone']),
            'num_document'  => preg_replace('/\D/', '', $param['num_document']),
            'password'      => (isset($param['password']) && !empty($param['password'])),
            'status'        => (isset($param['status']) && !empty($param['status'])) ? $param['status'] : 1
        );

        $this->session->form = array('user' => $toSave);

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

    // TODO: OLD
    /**
     * Action Lista
     */
    public function listaAction() {
        $auth = new Application_Model_Auth();
        if ($auth->hasPerm($this->session->atendente['permissoes'], 'import-user')) {
            $this->_helper->redirector('denied', 'Auth');
        }

        $search = $this->getRequest()->getQuery('search', null);
        $where = '';
        if ($search) {
            if ($this->session->atendente['superuser'] == 1) {
                $where = ' WHERE (LOWER(user.name) like "%' . strtolower($search) . '%" OR 
                                 user.username = "' . strtolower($search) . '") ';
            } else {
                $where = ' AND (LOWER(user.name) like "%' . strtolower($search) . '%" OR 
                                 user.username = "' . strtolower($search) . '") ';
            }
        }

        $atendente = new Application_Model_Atendente();

        $limit = PAGINATOR_LIMIT;
        if ($this->getRequest()->getParam('p')) {
            $page = $this->getRequest()->getParam('p');
        } else {
            $page = 1;
        }
        $offset = ($page * $limit) - $limit;
        $atendenteTotal = $atendente->total();
        $pagination = array(
            'page' => $page,
            'total_pages' => ceil($atendenteTotal / $limit),
            'link' => $this->view->baseUrl() . '/atendente/lista',
        );

        $limit = ' LIMIT ' . $offset . ',' . $limit . '';

        // Se for SuperUser lista todos os atendentes
        if ($this->session->atendente['superuser'] == 1) {
            $atendenteFetch = $atendente->getSuperUserList($where, $limit);

            // Se não for SuperUser lista atendentes de acordo com a Empresa
        } else {
            $atendenteFetch = $atendente->getUserWorkplaceList($this->session->atendente['id'], $this->session->atendente['workplace'], $where, $limit);
        }

        // Seta variáveis na View
        $this->view->rows = $atendenteFetch;
        $this->view->title = "Administração de atendentes / Lista";
        $this->view->menu_adm_user_lista_active = 'class=active';
        $this->view->search = $search;
        $this->view->pagination = $pagination;
    }

    /**
     * Action Apagar
     * @method  GET
     * param: id do usuário
     */
    public function apagarAction() {
        if (!$this->getRequest()->getParam('i')) {
            $this->_helper->FlashMessenger(
                    array('error' => 'Atendente não encontrado.')
            );
            $this->_helper->redirector('lista', 'atendente');
        }
        $id = $this->getRequest()->getParam('i');
        $atendente = new Application_Model_Atendente($id);
        $atendente->execute_sql('DELETE FROM user_has_permission WHERE iduser=' . $id);
        try {
            $atendente->del();
            $this->_helper->FlashMessenger(
                    array('success' => 'Atendente deletado com sucesso.')
            );
        } catch (Exception $e) {
            $this->_helper->FlashMessenger(
                    array('error' => 'Não foi possível deletar o usuário pois existem dependencias de log.')
            );
        }

        // Grava log de Administração
        $atendente->addAdmLog(array('action' => 'delete-user',
            'extra' => json_encode(array($atendente->name, $id)),
            'id_user' => $this->session->atendente['id'],
        ));

        $this->_helper->redirector('lista', 'Atendente');
    }

    /**
     * Método para resetar a senha
     * @method  POST
     */
    public function resetarSenhaAction() {
        if (!$this->getRequest()->getParam('i')) {
            $this->_helper->FlashMessenger(
                    array('error' => 'Atendente não encontrado.')
            );
            $this->_helper->redirector('lista', 'atendente');
        }
        // Pega o atendente
        $atendente = new Application_Model_Atendente($this->getRequest()->getParam('i'));

        // Verifica se foi POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Recebe valores do Post
            $senhaResetarSenha = $this->getRequest()->getPost('senhaResetarSenha');
            $confsenhaResetarSenha = $this->getRequest()->getPost('confsenhaResetarSenha');
            $search = $this->getRequest()->getPost('senhaSearch', null);

            if ($atendente->validatePassword($senhaResetarSenha)) {

                // Verifica se senha e confirmação sao iguais
                if ($senhaResetarSenha != $confsenhaResetarSenha) {
                    $this->_helper->FlashMessenger(
                            array('error' => 'Senha e confirmação de senha diferentes.')
                    );
                    $this->redirect("/atendente/lista/?search=" . $search);
                } else {
                    // Atualiza senha
                    $atendente->execute_sql('UPDATE user SET password = MD5("' . $senhaResetarSenha . '") 
                                                                  WHERE id = ' . $this->getRequest()->getParam('i'));
                    $this->_helper->FlashMessenger(
                            array('success' => 'Senha resetada com sucesso.')
                    );

                    // Grava log de Administração
                    $atendente->addAdmLog(array('action' => 'reset-user-passwrod',
                        'extra' => json_encode(array($atendente->name, $this->getRequest()->getParam('i'))),
                        'id_user' => $this->session->atendente['id'],
                    ));

                    if (!$search) {
                        $this->_helper->redirector('lista', 'atendente');
                    } else {
                        $this->redirect("/atendente/lista/?search=" . $search);
                    }
                }
            } else {
                $this->_helper->FlashMessenger(
                        array('error' => 'Senha inválida.')
                );
                $this->redirect("/atendente/lista/?search=" . $search);
            }
        }
    }

    /**
     * Ativar e Desativar Usuário
     * @method  GET
     */
    public function ativarDesativarAction() {
        if (!$this->getRequest()->getParam('i')) {
            $this->_helper->FlashMessenger(
                    array('error' => 'Atendente não encontrado.')
            );
            $this->_helper->redirector('lista', 'atendente');
        }

        $id = $this->getRequest()->getParam('i');
        $atendente = new Application_Model_Atendente($id);

        if ($atendente->enabled == 1) {
            $atendente->set_values(array('enabled' => -1));
            $action = 'deactivate-user';
        } else {
            $atendente->set_values(array('enabled' => 1));
            $action = 'activate-user';
        }
        $atendente->save();

        // Grava log de Administração
        $atendente->addAdmLog(array('action' => $action,
            'extra' => json_encode(array($atendente->name, $id)),
            'id_user' => $this->session->atendente['id'],
        ));

        $this->_helper->FlashMessenger(
                array('success' => 'Atendente alterado com sucesso.')
        );
        $this->_helper->redirector('lista', 'atendente');
    }
}