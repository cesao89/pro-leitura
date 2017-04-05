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
        # Verifica se esta logado
        if ($this->auth->is_logged($this->session)){
            # Verifica se é gestor
            if($this->session->usuario['profile'] != 'gestor')
                $this->_helper->redirector('perfil', 'usuario');
        }

        $usuario = new Application_Model_Usuario();

        if ($this->session->usuario['profile'] != 'gestor')
            $usuario->fields['profile_id']['form'] = false;

        if(isset($this->session->form['user']) && !empty($this->session->form['user']))
            $usuario->set_values($this->session->form['user']);

        # Seta variáveis na View
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
            $this->_helper->FlashMessenger(array('error' => 'Ocorreu um erro para cadastrar o usuário.'));
        }

        $param = $this->getAllParams();

        $toSave = array(
            'profile_id'    => (isset($param['profile_id']) && !empty($param['profile_id'])) ? $param['profile_id'] : 2,
            'name'          => $param['name'],
            'email'         => (isset($param['email']) && !empty($param['email'])) ? $param['email'] : null,
            'phone'         => preg_replace('/\D/', '', $param['phone']),
            'num_document'  => preg_replace('/\D/', '', $param['num_document']),
            'password'      => (isset($param['password']) && !empty($param['password'])) ? $param['password'] : null,
            'status'        => (isset($param['status']) && !empty($param['status'])) ? $param['status'] : 1
        );

        $this->session->form = array('user' => $toSave);

        $usuario = new Application_Model_Usuario();
        if(isset($param['i']) && !empty($param['i'])){
            unset($toSave['email']);
            $toSave['password'] = md5($toSave['password']);
            $usuario->get($param['i']);
        }

        if(!isset($toSave['password']) || empty($toSave['password']))
            unset($toSave['password']);

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
                $this->_helper->FlashMessenger(array('error' => 'Ocorreu um erro para cadastrar o usuário.'));
            }
        } else {
            $this->_helper->FlashMessenger(array('error' => 'Dados inválido.'));
        }

        $this->_helper->redirector('cadastro', 'usuario');
    }

    public function adminAction()
    {
        # Verifica se esta logado
        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        # Verifica se é gestor
        if($this->session->usuario['profile'] != 'gestor')
            $this->_helper->redirector('perfil', 'usuario');

        $usuarioModel = new Application_Model_Usuario();

        $this->view->title = 'Administração de Usuários';
        $this->view->list = $usuarioModel->filter();
    }

    public function statusAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        # Verifica se esta logado
        if (!$this->auth->is_logged($this->session))
            $this->_helper->redirector('login', 'usuario');

        # Verifica se é gestor
        if($this->session->usuario['profile'] != 'gestor')
            $this->_helper->redirector('perfil', 'usuario');

        $userId = $this->getRequest()->getParam('i', null);
        $statusId = $this->getRequest()->getParam('s', null);

        # Verifica se foi passado o usuário
        if(!isset($userId) || empty($userId))
            $this->_helper->redirector('perfil', 'usuario');

        # Verifica se foi passado o status para editar
        if(!isset($statusId) || empty($statusId))
            $this->_helper->redirector('perfil', 'usuario');

        $usuarioModel = new Application_Model_Usuario($userId);
        $usuarioModel->set_values(array('status' => $statusId));
        $usuarioModel->save();

        $this->_helper->redirector('admin', 'usuario');
    }

    public function esqueciSenhaAction()
    {
        $newPassword = $this->randomPassword();
        $subject = 'Recuperar Senha - Plataforma Pro-Livro';
        $body = 'Ol&aacute;,<br /><br />Sua nova senha &eacute;: '. $newPassword .'<br />Voc&ecirc; pode alterar sua senha acessando o sistema e clicando no <b>SEU NOME > EDITAR</b>.<br /><br />Plataforma - Pr&oacute;-Livro';

        $email = $this->getRequest()->getParam('mail', null);

        # Verifica se é e-mail válido
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            die("E-mail inválido, por favor digite um e-mail válido");

        # Pesquisa e-mail
        $usuarioModel = new Application_Model_Usuario();
        $findUser = $usuarioModel->where(array('email' => $email))->limit(0,1)->filter();

        # Verifica se encontrou algum usuário
        if(!isset($findUser[0]->id) || empty($findUser[0]->id))
            die("Usuário não encontrado, por favor verifique o e-mail digitado");

        # Estancia o usuário e altera a senha com MD5
        $usuarioModel->get($findUser[0]->id);
        $usuarioModel->set_values(array('password' => md5($newPassword)));
        $usuarioModel->save();

        $return = $this->sendMail($email, $subject, $body);
        die($return);
    }

    private function sendMail($email, $subject, $body)
    {
        require APPLICATION_PATH .'/../library/PHPMailer-master/PHPMailerAutoload.php';

        $mail = new PHPMailer;

//        $mail->SMTPDebug = 1;                     // debugging: 1 = errors and messages, 2 = messages only

        $mail->isSMTP();                    // Set mailer to use SMTP
        $mail->Host         = MAIL_HOST;    // Specify main and backup SMTP servers
        $mail->SMTPAuth     = MAIL_AUTH;    // Enable SMTP authentication
        $mail->Username     = MAIL_USER;    // SMTP username
        $mail->Password     = MAIL_PASS;    // SMTP password
        $mail->SMTPSecure   = MAIL_SECURE;  // Enable TLS encryption, `ssl` also accepted
        $mail->Port         = MAIL_PORT;    // TCP port to connect to

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($email);          // Add a recipient
        $mail->isHTML(true);                // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $body;

        if(!$mail->send()) {
            echo 'Houve uma falha no envio do e-mail, por favor tente novamente mais tarde.';
        } else {
            echo 'Um e-mail foi enviado para você com uma nova senha!';
        }
    }

    private function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}