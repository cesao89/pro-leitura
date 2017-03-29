<?php

/**
 * Model Auth
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 * @version 1.0
 * @package classes
 */
class Application_Model_Auth extends Application_Model_BaseDum
{
    public $msg = NULL;   // array de mensagem
    public $error = NULL; // array de errors
    public $conn = NULL;  // instancia da conexão DB

    // Config para classe Abstrata BaseDum
    private $tb_auth = 'user';   // tabela de autenticação
    private $idx = 'id';         // pk
    protected $config = array(); // Zend application.ini

    /**
     * Application_Model_Auth constructor.
     */
    public function __construct() {
        // Pega valores do application.ini
        $this->config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $this->config);

        // Define conexão com DB
        $this->conn = parent::dbConn(
            $this->config->resources->multidb->proleitura->host,
            $this->config->resources->multidb->proleitura->username,
            $this->config->resources->multidb->proleitura->password,
            $this->config->resources->multidb->proleitura->dbname
        );
    }

    /**
     * Método para autenticar o usuário
     * @param $usuario
     * @param $senha
     * @param bool $md5
     * @return bool
     */
    public function autenticar($usuario, $senha, $md5 = true) {
        if ($md5)
            $senha = md5($senha);

        $sql = "SELECT 
                    u.`id` as id
                    , p.`name` as profile
                    , u.`name` as name
                    , u.`email` as email
                    , u.`phone` as phone
                    , u.`num_document` as num_document 
                FROM 
                    ". $this->tb_auth ." as u 
                    JOIN profile as p ON p.`id`=u.`profile_id`
                WHERE u.`email` = ". $this->conn->quote($usuario) ."
                    AND u.`password` = '" . $senha . "' 
                    AND u.`status`=1 
                    AND p.`status`=1
                LIMIT 1
        ";

        $result = $this->conn->fetchAll($sql);
        if (count($result) > 0) {
            $this->msg = 'Login efetuado com sucesso.';
            return $result[0];
        } else {
            $this->msg = 'Usuário ou senha não encontrados.';
            return false;
        }
    }

    /**
     * Método para verificar se esta logado
     * @param $sessao
     * @return bool
     */
    public function is_logged($sessao) {
        if (isset($sessao->logado) && $sessao->logado === true)
            return true;
        return false;
    }
}