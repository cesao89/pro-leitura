<?php

/**
 * Class ProjetoDetalhes
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_ProjetoDetalhes extends Application_Model_BaseDum
{
    public $db;
    public $fields = null;                                      // propriedades
    protected $classname = 'Application_Model_ProjetoDetalhes'; // nome da classe
    protected $table = 'projeto_detalhes';                      // tabela
    protected $pk = 'project_id';                               // chave primária
    protected $auto_increment = true;                           // autoincrement
    protected $db_config = array();                             // config do acesso ao DB
    protected $config = array();                                // config ( application.ini )
    private $conn;

    /**
     * Application_Model_ProjetoDetalhes constructor.
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
            'sintese' => array(
                'name' => 'sintese',
                'title' => 'Síntese',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'caracteristicas' => array(
                'name' => 'caracteristicas',
                'title' => 'Características',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'objetivos' => array(
                'name' => 'objetivos',
                'title' => 'Objetivos Geral e Específicos',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'justificativas' => array(
                'name' => 'justificativas',
                'title' => 'Justificativas',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'metodologia_a' => array(
                'name' => 'metodologia_a',
                'title' => 'Metodologia: Questão (A)',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'metodologia_b' => array(
                'name' => 'metodologia_b',
                'title' => 'Metodologia: Questão (B)',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'resultado' => array(
                'name' => 'resultado',
                'title' => 'Resultados Alcançados',
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