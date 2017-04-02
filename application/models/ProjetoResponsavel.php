<?php

/**
 * Class ProjetoResponsavel
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_ProjetoResponsavel extends Application_Model_BaseDum
{
    public $db;
    public $fields = null;                                          // propriedades
    protected $classname = 'Application_Model_ProjetoResponsavel';  // nome da classe
    protected $table = 'projeto_responsavel';                       // tabela
    protected $pk = 'project_id';                                   // chave primária
    protected $auto_increment = true;                               // autoincrement
    protected $db_config = array();                                 // config do acesso ao DB
    protected $config = array();                                    // config ( application.ini )
    private $conn;

    /**
     * Application_Model_ProjetoResponsavel constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
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

        // Define conexão com DB
        $this->db = parent::getConn();

        /**
         * Propriedades e regras para validação
         * Propriedades devem ser iguais aos campos da tabela caso utilize DB
         * Definição:
         *
         * 1 - array(); Chave deve ser a propriedade
         * 2 - array de Atributos contemplados:
         *      name        -> chave ( propriedade )
         *      title       -> Nome da Propriedade (View)
         *      type        -> Tipo de dado: num, char, date, password
         *      required    -> true / false
         *      list        -> lista de valores ( caso de select, radio, checkbox )
         *      md5         -> criptografa valor para MD5
         *      default     -> valor default
         *      auto        -> true / false define se o valor é automaticamente preenchido
         *      form        -> array com definições para formulário / false:
         *          type        -> tipo do campo (textfield, password, select)
         *          css         -> css do campo
         */
        $this->fields = array(
            'project_id' => array(
                'name' => 'project_id',
                'title' => 'ID do Projeto',
                'type' => 'num',
                'required' => true,
                'form' => false
            ),
            'organizacao' => array(
                'name' => 'organizacao',
                'title' => 'Organização/Instituição',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'cnpj' => array(
                'name' => 'cnpj',
                'title' => 'CNPJ',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'cidade' => array(
                'name' => 'cidade',
                'title' => 'Cidade',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'uf' => array(
                'name' => 'uf',
                'title' => 'Estado',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'cep' => array(
                'name' => 'cep',
                'title' => 'CEP',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'email' => array(
                'name' => 'email',
                'title' => 'E-mail',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'telefone' => array(
                'name' => 'telefone',
                'title' => 'Telefone',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'celular' => array(
                'name' => 'celular',
                'title' => 'Celular',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'site' => array(
                'name' => 'site',
                'title' => 'Site',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'facebook' => array(
                'name' => 'facebook',
                'title' => 'Facebook',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'outros_contatos' => array(
                'name' => 'outros_contatos',
                'title' => 'Outros Contatos',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'pessoa_responsavel' => array(
                'name' => 'pessoa_responsavel',
                'title' => 'Pessoa Responsavel',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'pessoa_cargo' => array(
                'name' => 'pessoa_cargo',
                'title' => 'Cargo da Pessoa',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'pessoa_email' => array(
                'name' => 'pessoa_email',
                'title' => 'E-mail da Pessoa',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'pessoa_telefone' => array(
                'name' => 'pessoa_telefone',
                'title' => 'Telefone da Pessoa',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'pessoa_celular' => array(
                'name' => 'pessoa_celular',
                'title' => 'Celular da Pessoa',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'pessoa_outros_contatos' => array(
                'name' => 'pessoa_outros_contatos',
                'title' => 'Outros Contatos da Pessoa',
                'type' => 'char',
                'required' => false,
                'form' => false
            )
        );

        // Define propriedades na classe abstrata
        parent::config($this);

        // Caso passe o ID já cria as propriedades necessários
        if ($id)
            parent::get($id);
    }
}