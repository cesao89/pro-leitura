<?php

/**
 * Class Application_Model_Usuario
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_Usuario extends Application_Model_BaseDum
{
    public $db;
    public $fields = null;              // propriedades
    protected $classname = 'Application_Model_Usuario'; // nome da classe
    protected $table = 'user';          // tabela
    protected $pk = 'id';               // chave primária
    protected $auto_increment = true;   // autoincrement
    protected $db_config = array();     // config do acesso ao DB
    protected $config = array();        // config ( application.ini )

    /**
     * Application_Model_Usuario constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        // Pega valores do application.ini
        $this->config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $this->config);

        // Define propriedades de conexão na classe
        parent::dbConn(
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
            'id' => array(
                'name' => 'id',
                'title' => 'ID',
                'type' => 'num',
                'required' => true,
                'auto' => true,
                'form' => false
            ),
            'profile_id' => array(
                'name' => 'profile_id',
                'title' => 'Perfil',
                'type' => 'num',
                'required' => true,
                'list' => array(1 => 'gestor', 2 => 'proponente'),
                'form' => array('type' => 'select', 'css' => 'input-large')
            ),
            'name' => array(
                'name' => 'name',
                'title' => 'Nome',
                'type' => 'char',
                'size' => 200,
                'validate' => 'validateName',
                'required' => true,
                'form' => array(
                    'type' => 'textfield',
                    'css' => 'input-large'
                ),
            ),
            'email' => array(
                'name' => 'email',
                'title' => 'E-mail',
                'type' => 'char',
                'size' => 200,
                'required' => true,
                'form' => array(
                    'type' => 'textfield',
                    'css' => 'input-large',
                ),
            ),
            'phone' => array(
                'name' => 'phone',
                'title' => 'Telefone',
                'type' => 'num',
                'size' => 15,
                'required' => true,
                'form' => array(
                    'type' => 'textfield',
                    'css' => 'input-large',
                ),
            ),
            'num_document' => array(
                'name' => 'num_document',
                'title' => 'CPF',
                'type' => 'num',
                'size' => 14,
                'required' => true,
                'form' => array(
                    'type' => 'textfield',
                    'css' => 'input-large',
                ),
            ),
            'password' => array(
                'name' => 'password',
                'title' => 'Senha',
                'type' => 'password',
                'required' => false,
                'md5' => true,
                'validate' => 'validatePassword',
                'form' => array(
                    'type' => 'password',
                    'css' => 'input-large',
                ),
            ),
            'status' => array(
                'name' => 'status',
                'title' => 'Status',
                'type' => 'num',
                'required' => false,
                'default' => 1,
                'form' => false,
            ),
            'created_at' => array(
                'name' => 'created_at',
                'title' => 'Data de Criação',
                'type' => 'date',
                'required' => false,
                'form' => false,
            ),
            'updated_at' => array(
                'name' => 'updated_at',
                'title' => 'Data de Atualização',
                'type' => 'date',
                'required' => false,
                'default' => date('Y-m-d H:i:s'),
                'auto' => true,
                'form' => false,
            )
        );

        // Define propriedades na classe abstrata
        parent::config($this);

        // Caso passe o ID já cria as propriedades necessários
        if ($id)
            parent::get($id);
    }

    /** TODO: ajustar
     * Método para validar a senha
     * @param $value
     * @return bool
     */
    public function validatePassword($value) {
        /*
          Passwords will contain at least (1) upper case letter
          Passwords will contain at least (1) lower case letter
          Passwords will contain at least (1) number or special character
          Passwords will contain at least (8) characters in length
          Password maximum length (20)
          syntax: (?=^.{8,20}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$
         */
//        if (!preg_match("/(?=^.{8,20}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/i", trim($value))) {
//            return false;
//        } else {
            return true;
//        }
    }

    /** TODO: ajustar
     * Método para validar o Nome completo
     * @param $value
     * @return bool
     */
    public function validateName($value) {
//        if (!preg_match("/^([a-zA-ZéúíóáÉÚÍÓÁèùìòàÈÙÌÒÀõãñÕÃÑêûîôâÊÛÎÔÂëÿüïöäËYÜÏÖÄ'-]+\s+){1,4}[a-zA-zéúíóáÉÚÍÓÁèùìòàÈÙÌÒÀõãñÕÃÑêûîôâÊÛÎÔÂëÿüïöäËYÜÏÖÄ'-]+$/i", trim($value))) {
//            return false;
//        } else {
            return true;
//        }
    }

    /*
     * Método para salvar o log de troca de senha
     */
    public function logChangePassword($arr){
        if (is_array($arr)){
            // Cria array para salvar
            $arr_save = array(
                'user_id' => $arr['user_id'],
                'password' => $arr['password'],
            );
            try {
                $this->db->insert('user_change_password', $arr_save);
                return true;
            } catch(Exception $e){
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * Método recursivo para gerar senha randomica
     *       seguindo a regra de validação de senha
     * return: string de senha
     */
    public function randomPassword() {
        // Upper alph
        $uppercase = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
        // Lower alph
        $lowercase = "abcdefghijklmnopqrstuwxyz";
        // Numbers
        $numbers = "0123456789";
        // Special chars
        $especial = "!@#$%&*?!+=";

        $password = array();
        // Especial chars: 1 caracter
        array_push($password, $especial[rand(0, strlen($especial) - 1)]);
        // Upper char: 3 caracter
        $upperlen = strlen($uppercase) - 1;
        for ($i = 0; $i < 3; $i++) {
            array_push($password, $uppercase[rand(0, $upperlen)]);
        }
        // Lower char: 2 caracter
        $lowerlen = strlen($lowercase) - 1;
        for ($i = 0; $i < 2; $i++) {
            array_push($password, $lowercase[rand(0, $lowerlen)]);
        }
        // Number char: 2 caracter
        $numberslen = strlen($numbers) - 1;
        for ($i = 0; $i < 2; $i++) {
            array_push($password, $numbers[rand(0, $numberslen)]);
        }

        // Ordena randomicamente o array
        shuffle($password);

        // Gera string do password
        $pass = implode($password);

        // Valida o password
        if (self::validatePassword($pass)) {
            return $pass;
        } else {
            return self::randomPassword();
        }
    }
}