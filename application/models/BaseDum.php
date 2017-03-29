<?php

/**
 * Class Application_Model_BaseDum
 * @author: Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_BaseDum extends Application_Model_DbConn
{
    protected $table = null;            // Tabela referente no DB
    protected $pk = null;               // Nome da PK no DB
    protected $fields = Array();        // Array de propriedades do BD
    protected $start_pk = null;         // Valor de inicio do PK
    protected $auto_increment = null;   // Define se é auto incremental
    private $conn = null;               // Instancia de conexão
    private $instance = null;           // Instancia da Model parent
    private $_sql = null;               // String de execução
    private $_sql_select = null;        // String de SELECT
    private $_sql_from = null;          // String de FROM
    private $_sql_where = '';           // String de WHERE
    private $_sql_order_by = null;      // String de ORDER BY
    private $_sql_limit = null;         // String de LIMIT
    private $_sql_offset = null;        // String de OFFSET
    public $errors = Array();           // Array de errors

    /**
     * Método de conexão ao DB
     *
     * @param null $host
     * @param null $username
     * @param null $password
     * @param null $database
     * @return null|Zend_Db_Adapter_Pdo_Mysql
     */
    public function dbConn($host = null, $username = null, $password = null, $database = null)
    {
        try {
            $this->conn = parent::connect($host, $username, $password, $database);
        } catch (Exception $e) {
            $this->conn = null;
        }
        return $this->conn;
    }

    /**
     * Método paa retornar a instancia da conexao
     *
     * @return null
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * Método para setar a instancia da conexao
     *
     * @param $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Define variaveis gerais da model e do objeto
     *
     * @param $inst
     */
    protected function config($inst)
    {
        $this->instance = $inst;
        $this->table = $this->instance->table;
        $this->pk = $this->instance->pk;
        $this->fields = $this->instance->fields;
        $this->start_pk = $this->instance->start_pk;
        $this->auto_increment = $this->instance->auto_increment;

        if (is_array($this->instance->db_config)){
            if (isset($this->instance->db_config['host'])){
                $this->conn = $this->dbConn(
                    $this->instance->db_config['host'],
                    $this->instance->db_config['username'],
                    $this->instance->db_config['password'],
                    $this->instance->db_config['dbname']
                );
            }
        }
        // Define charset na conexão
        $this->execute_sql('SET NAMES utf8');
    }

    /**
     * Método para buscar um registro
     * As propriedades são geradas na model parent: Ex.  Model->propriedade
     *
     * @param $id
     * @return $this
     */
    public function get($id)
    {
        $this->clear();

        if ($this->_sql_select == null) {
            $this->select($this->_only_fields());
        }

        if ($this->_sql_from == null) {
            $this->from($this->table);
        }

        $arr_where = array($this->pk => $id);
        $this->where($arr_where);
        $result = $this->run();

        if ($result) {
            $this->_map_values($result[0]);
        }

        $this->bindArrayToObject($this->_only_values());
        return $this;
    }

    /**
     * Método para filtrar consulta
     *
     * @param null $arr
     * @return array
     */
    public function filter($arr = null)
    {
        if ($this->_sql_select == null) {
            $this->select($this->_only_fields());
        }

        if ($this->_sql_from == null) {
            $this->from($this->table);
        }

        if ($this->_sql_order_by == null) {
            $this->order_by();
        }

        return get_object_vars($this->arrayToObject($this->run()));
    }

    /**
     * Método para executar um SQL
     *
     * @param $sql
     * @return array
     */
    public function free_select($sql)
    {
        $this->_sql_select = $this->clearInjection($sql);
        return get_object_vars($this->arrayToObject($this->run()));
    }

    /**
     * Método para executar um SQL
     *
     * @param $sql
     * @return null
     */
    public function free_select_array($sql)
    {
        $this->_sql_select = $this->clearInjection($sql);
        return $this->run();
    }

    /**
     * Método executar um SQL
     *
     * @param $sql
     * @return mixed
     */
    public function execute_sql($sql)
    {
        try {
            return $this->conn->exec($this->clearInjection($sql));
        } catch (Exception $e){
            if(DEBUG_BASEDUM)
                die($e->getMessage());
            die('OOps. Ocorreu um erro.');
        }
    }

    /**
     * Método executar um SQL
     *
     * @return null
     */
    public function run()
    {
        $sql = $this->_sql_select . $this->_sql_from . $this->_sql_where . $this->_sql_order_by;

        if ($this->_sql_limit) {
            $sql .= $this->_sql_limit;
        }

        if ($this->_sql_offset) {
            $sql .= " , " . $this->_sql_offset;
        }

        try{
            $result = $this->conn->fetchAll($this->clearInjection($sql));
        } catch (Exception $e){
            if(DEBUG_BASEDUM)
                die($e->getMessage());
            die('OOps. Ocorreu um erro.');
        }

        if (count($result) > 0) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Método executar um SQL
     *
     * @return string
     */
    public function get_sql()
    {
        if ($this->_sql_select == null) {
            $this->select($this->_only_fields());
        }

        if ($this->_sql_from == null) {
            $this->from($this->table);
        }

        if ($this->_sql_order_by == null) {
            $this->order_by();
        }

        $sql = $this->_sql_select . $this->_sql_from . $this->_sql_where . $this->_sql_order_by;

        if ($this->_sql_limit) {
            $sql .= $this->_sql_limit;
        }

        if ($this->_sql_offset) {
            $sql .= " , " . $this->_sql_offset;
        }

        return $sql;
    }

    /**
     * Método para retornar o total de registros
     *
     * @return int
     */
    public function total()
    {
        if ($this->_sql_from == null) {
            $this->from($this->table);
        }

        $sql = "SELECT count(" . $this->pk . ") AS total " . $this->_sql_from . $this->_sql_where;

        try {
            $result = $this->conn->fetchAll($this->clearInjection($sql));
        } catch (Exception $e){
            if(DEBUG_BASEDUM)
                die($e->getMessage());
            return 0;
        }

        if (count($result) > 0) {
            return intval($result[0]['total']);
        } else {
            return 0;
        }
    }

    /**
     * Método para definir a ordenaçao
     *
     * @param null $field
     * @return $this
     */
    public function order_by($field = null)
    {
        if ($field == null) {
            $field = '-' . $this->pk;
        }

        $dir = (strstr($field, '-')) ? 'DESC' : 'ASC';
        $field = str_replace('-', '', $field);
        $this->_sql_order_by = " ORDER BY " . $field . " " . $dir;
        return $this;
    }

    private function _getSymbol($k, $default = '=')
    {
        if (strstr($k, '__')){
            list($k, $symbol) = explode('__', $k);
            switch ($symbol){
                case 'gt':  $return = '>';  break;
                case 'gte': $return = '>='; break;
                case 'lt':  $return = '<';  break;
                case 'lte': $return = '<='; break;
                default: $return = '=';
            }
            return array('k' => $k, 'symbol' => $return);
        } else {
            return array('k' => $k, 'symbol' => $default);
        }
    }

    /**
     * Método para definir o where
     *
     * @param $arr
     * @param string $type
     * @param bool $incr
     * @return $this
     */
    public function where($arr, $type = 'AND', $incr = false)
    {
        $sql = $this->_sql_where;

        if (!$incr) {
            $sql = " WHERE (";
        } else {
            if ($incr){
                $sql .= " ". $type ." (";
            } else {
                $sql .= " AND (";
            }
        }

        if (is_array($arr)) {
            $aux = array();

            foreach ($arr as $k => $v) {
                $v = $this->escape($v);
                $fk = $this->_getSymbol($k);
                $k = $fk['k'];

                if ($v == null) {
                    array_push($aux, $k . " IS NULL");
                } else {
                    if (!is_numeric($v)) {
                        $v = "'" . $v . "'";
                    }

                    array_push($aux, $k . $fk['symbol'] . $v);
                }
            }

            $sql .= implode(' ' . $type . ' ', $aux);
        }

        $this->_sql_where = "" . $sql . ")";
        return $this;
    }

    /**
     * Método para definir o where
     *
     * @param $arr
     * @param string $type
     * @param bool $incr
     * @return $this
     */
    public function whereNot($arr, $type = 'AND', $incr = false)
    {
        $sql = $this->_sql_where;

        if (!$incr) {
            $sql = " WHERE (";
        } else {
            $sql .= ($incr) ? " ". $type ." (" : " AND (";
        }

        if (is_array($arr)) {
            $aux = array();

            foreach ($arr as $k => $v) {
                $v = $this->escape($v);
                $fk = $this->_getSymbol($k, '<>');
                $k = $fk['k'];

                if ($v == null) {
                    array_push($aux, $k . " IS NULL");
                } else {
                    if (!is_numeric($v)) {
                        $v = "'" . $v . "'";
                    }

                    array_push($aux, $k . $fk . $v);
                }
            }

            $sql .= implode(' ' . $type . ' ', $aux);
        }

        $this->_sql_where = "" . $sql . ")";
        return $this;
    }

    /**
     * Método para definir os campos selecionados
     *
     * @param $arr
     * @return $this
     */
    public function select($arr)
    {
        $fields = (is_array($arr)) ? implode(',', $arr) : $arr;
        $this->_sql_select = "SELECT " . $fields . " ";
        return $this;
    }

    /**
     * Método para definir o FROM
     *
     * @param $table
     * @return $this
     */
    public function from($table)
    {
        $this->_sql_from = "FROM " . $table . " ";
        return $this;
    }

    /**
     * Método para Salvar registro
     *
     * @return bool
     */
    public function save()
    {
        $arr_val = $this->_only_values();

        if (!array_key_exists("value", $this->fields[$this->pk])) {
            return $this->_insert($arr_val);
        } else {
            return $this->_update($arr_val);
        }
    }

    /**
     * Método para validar valores antes de salvar
     *
     * @param $arr
     * @param $type
     * @return array
     */
    private function _validate_fields($arr, $type)
    {
        $arr = $this->escape($arr);
        $this->errors = Array();
        $arr_fields = Array();

        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                $bln = true;
                if (isset($this->fields[$k])) {
                    $rules = $this->fields[$k];

                    // Regras baseadas na model class
                    $auto = (array_key_exists("auto", $rules)) ? $rules['auto'] : false;

                    if ($auto == false) {
                        // Verifica se é requerido
                        if ($rules['required'] == true && ($rules['value'] == null || $rules['value'] == '')) {
                            $this->fields[$k]['error'] = 'O campo ' . $rules['title'] . ' é obrigatório.';
                            array_push($this->errors, array(
                                'error' => 'O campo ' . $rules['title'] . ' é obrigatório.',
                                'field' => $k,
                                'value' => $rules['value'],
                            ));
                            $bln = false;
                        } else {
                            // Verifica se é número
                            if ($rules['type'] == 'num') {
                                if (isset($rules['value'])) {
                                    if (!is_numeric($rules['value'])) {
                                        $this->fields[$k]['error'] = 'O campo ' . $rules['title'] . ' aceita somente números.';
                                        array_push($this->errors, array(
                                            'error' => 'O campo ' . $rules['title'] . ' aceita somente números.',
                                            'field' => $k,
                                            'value' => $rules['value'],
                                        ));
                                        $bln = false;
                                    }
                                }
                            }

                            // Verifica se excedeu o tamaho de caracteres
                            if ($rules['type'] == 'char' and isset($rules['size'])) {
                                $total = strlen($rules['value']);
                                if ($total > $rules['size']) {
                                    $this->fields[$k]['error'] = 'Execeu o limite de caracteres do campo ' . $rules['title'] . '.';
                                    array_push($this->errors, array(
                                        'error' => 'Execeu o limite de caracteres do campo ' . $rules['title'] . '.',
                                        'field' => $k,
                                        'value' => $rules['value'],
                                    ));
                                    $bln = false;
                                }
                            }

                            // Valida email
                            if ($rules['type'] == 'email') {
                                if (!$this->_check_email($rules['value'])) {
                                    $this->fields[$k]['error'] = 'O email ' . $rules['title'] . ' é inválido.';
                                    array_push($this->errors, array(
                                        'error' => 'O email ' . $rules['title'] . ' é inválido.',
                                        'field' => $k,
                                        'value' => $rules['value'],
                                    ));
                                    $bln = false;
                                }
                            }

                            // Valida data
                            if ($rules['type'] == 'date') {
                                $cd = (isset($rules['value'])) ? $this->_check_date($rules['value']) : false;

                                if (!$cd) {
                                    $this->fields[$k]['error'] = 'A data ' . $rules['title'] . ' é inválida.';
                                    $rv = (isset($rules['value'])) ? $rules['value'] : null;

                                    array_push($this->errors, array(
                                        'error' => 'A data ' . $rules['title'] . ' é inválida.',
                                        'field' => $k,
                                        'value' => $rv,
                                    ));
                                    $bln = false;
                                } else {
                                    $rules['value'] = $cd;
                                }
                            }

                            // Valida cpf
                            if ($rules['type'] == 'cpf') {
                                if (!$this->_check_cpf($rules['value'])) {
                                    $this->fields[$k]['error'] = 'O ' . $rules['title'] . ' é inválido.';
                                    array_push($this->errors, array(
                                        'error' => 'O cpf ' . $rules['title'] . ' é inválido.',
                                        'field' => $k,
                                        'value' => $rules['value'],
                                    ));
                                    $bln = false;
                                }
                            }

                            // Validate Dynamic
                            if ($type == 'validate') {
                                if (!empty($rules['validate'])) {
                                    if (!$this->validateMethods($this->instance->classname, $rules['validate'], $rules['value'])){
                                        $this->fields[$k]['error'] = 'O campo ' . $rules['title'] . ' é inválido.';
                                        array_push($this->errors, array(
                                            'error' => 'O campo ' . $rules['title'] . ' é inválido.',
                                            'field' => $k,
                                            'value' => $rules['value'],
                                        ));
                                        $bln = false;
                                    }
                                }
                            }

                            // Senha
                            if ($rules['type'] == 'password') {
                                if ($type == 'insert') {
                                    if (array_key_exists("md5", $rules)) {
                                        if ($rules['md5'] == true) {
                                            $rules['value'] = md5($rules['value']);
                                        }
                                    }
                                }
                            }

                            // Adiciona item validado
                            if ($bln) {
                                if (isset($rules['value'])){
                                    $arr_fields[$k] = $rules['value'];
                                }
                            }
                        }
                    } else {
                        // Adiciona valor default
                        if ($rules['auto'] == true) {
                            if ($type == 'insert') {
                                if (isset($rules['default'])) {
                                    $arr_fields[$k] = $rules['default'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arr_fields;
    }

    /**
     * Método para definir o FROM
     *
     * @param array $arr
     * @return bool
     */
    public function validate_form($arr = array())
    {
        $this->set_values($arr);
        $this->_validate_fields($arr, 'validate');
        if (count($this->errors) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Método para Inserir registro no DB
     *
     * @param $arr
     * @return bool
     */
    private function _insert($arr)
    {
        $arr_insert = $this->_validate_fields($arr, 'insert');

        if (count($arr_insert) > 0) {
            // Verifica se é auto increment
            if ($this->auto_increment == false) {
                $last_val = $this->get_last_pk();
                $arr_insert[$this->pk] = $last_val;
            }

            $exception=null;

            try {
                $bln = $this->conn->insert($this->table, $arr_insert);
            } catch (Exception $e) {
                $bln = false;
                $exception = $e;
                array_push($this->errors, array(
                    'error' => 'Ocorreu um erro para salvar o registro.',
                    'exception' => strval($e->getMessage()),
                ));
            }

            if ($bln) {
                $this->set_values(array($this->pk => $this->get_last_inserted()));
                return true;
            } else {
                array_push($this->errors, array(
                    'error' => 'Ocorreu um erro para salvar o registro.',
                    'exception' => strval($exception->getMessage()),
                ));
                return false;
            }
        } else {
            array_push($this->errors, array(
                'error' => 'Valores em branco',
                'exception' => 'count arr_insert: '. count($arr_insert),
            ));
            return false;
        }
    }

    /**
     * Método para Alterar registro no DB
     *
     * @param $arr
     * @return bool
     */
    private function _update($arr)
    {
        $arr_update = $this->_validate_fields($arr, 'update');

        if (count($arr_update) > 0) {
            if ($this->fields[$this->pk]['value']) {
                try {
                    $this->conn->update($this->table, $arr_update, $this->pk . ' = ' . $this->fields[$this->pk]['value']);
                    $bln = true;
                } catch (Exception $e) {
                    if(DEBUG_BASEDUM)
                        die($e->getMessage());
                    $bln = false;
                }
            } else {
                $bln = false;
            }

            if ($bln) {
                return true;
            } else {
                array_push($this->errors, array('error' => 'Ocorreu um erro para alterar o registro.'));
                return false;
            }
        } else {
            array_push($this->errors, array('error' => 'Ocorreu um erro para alterar o registro.'));
            return false;
        }
    }

    /**
     * Método para Deletar um registro
     *
     * @return mixed
     */
    public function del()
    {
        return $this->conn->delete($this->table, $this->pk . ' = ' . $this->fields[$this->pk]['value']);
    }

    /**
     * Método para retornar pk do ultimo registro
     *
     * @return null
     */
    public function get_last_pk()
    {
        $this->_sql = null;
        $this->_sql_select = null;
        $this->_sql_from = null;
        $this->_sql_where = '';
        $this->_sql_order_by = null;
        $this->_sql_limit = null;
        $this->_sql_offset = null;

        $result = $this->select(array($this->pk))->from($this->table)->order_by()->run();
        if ($result) {
            return $result[0][$this->pk];
        } else {
            return $this->start_pk;
        }
    }

    /**
     * Método para retornar pk do ultimo registro inserido
     *
     * @return null
     */
    public function get_last_inserted()
    {
        $this->_sql = null;
        $this->_sql_select = null;
        $this->_sql_from = null;
        $this->_sql_where = '';
        $this->_sql_order_by = null;
        $this->_sql_limit = null;
        $this->_sql_offset = null;

        try {
            $result = $this->free_select_array("SELECT LAST_INSERT_ID() AS last_id");
        } catch (Exception $e){
            if(DEBUG_BASEDUM)
                die($e->getMessage());
            $result = false;
        }

        if ($result) {
            return $result[0]['last_id'];
        } else {
            return $this->start_pk;
        }
    }

    /**
     * Método para setar uma propriedade
     *
     * @param $arr
     */
    public function set_values($arr)
    {
        $arr = $this->escape($arr);
        $this->_map_values($arr);
        $this->bindArrayToObject($arr);
    }

    /**
     * Método para converter um array para objecto na instancia parent
     *
     * @param $array
     * @return null
     */
    protected function bindArrayToObject($array)
    {
        $return = new stdClass();
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $this->instance->$k = $this->bindArrayToObject($v, $this->instance->$k);
            } else {
                $this->instance->$k = $v;
            }
        }
        return $this->instance;
    }

    /**
     * Método retornar propriedades e valores
     *
     * @return array
     */
    private function _only_values()
    {
        $arr = array();
        if (is_array($this->fields)) {
            foreach ($this->fields as $k => $v) {
                $arr[$k] = (isset($v['value'])) ? $v['value'] : null;
            }
        }
        return $arr;
    }

    /**
     * Método retornar propriedades
     *
     * @return array
     */
    private function _only_fields()
    {
        $arr = array();
        if (is_array($this->fields)) {
            foreach ($this->fields as $k => $v) {
                array_push($arr, $k);
            }
        }
        return $arr;
    }

    /**
     * Método setar valores na propriedade fields
     *
     * @param $arr
     */
    private function _map_values($arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                if (is_array($this->fields)) {
                    foreach ($this->fields as $k => $v) {
                        if ($k == $key) {
                            $this->fields[$key]['value'] = $value;
                        }
                    }
                }
            }
        }
    }

    /**
     * Método para converter um array para objecto
     *
     * @param $array
     * @return stdClass
     */
    protected function arrayToObject($array)
    {
        $return = new stdClass();
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    $return->$k = $this->arrayToObject($v);
                } else {
                    if ($v) {
                        $return->$k = $v;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Método de escape dos valores das propriedades recursivo
     *
     * @param $a
     * @return string
     */
    public function escape($a)
    {
        if (is_array($a)) {
            $b = null;
            foreach ($a as $k => $v) {
                if (is_array($v)){
                    $this->escape($v);
                } else {
                    $b[$k] = $this->escape(trim($v));
                }
            }
            return $b;
        } else {
            return addslashes($a);
        }
    }

    /**
     * Método para validar email com MX
     *
     * @param $value
     * @return bool
     */
    private function _check_email($value)
    {
        list($name, $dominio) = explode('@', $value);
        return checkdnsrr($dominio, "MX");
    }

    /**
     * Método para validar o CPF
     *
     * @param $cpf
     * @return bool
     */
    private function _check_cpf($cpf)
    {
        if ($cpf) {
            $cpf = preg_replace('/\D/', '', $cpf);
            $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

            if (strlen($cpf) != 11
                || $cpf == '00000000000'
                || $cpf == '11111111111'
                || $cpf == '22222222222'
                || $cpf == '33333333333'
                || $cpf == '44444444444'
                || $cpf == '55555555555'
                || $cpf == '66666666666'
                || $cpf == '77777777777'
                || $cpf == '88888888888'
                || $cpf == '99999999999') {
                return false;
            } else {
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf{$c} * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;

                    if ($cpf{$c} != $d) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Método para validar Data
     *
     * @param $data
     * @param string $formato
     * @return bool|string
     */
    private function _check_date($data, $formato = 'DD/MM/AAAA')
    {
        if ($data) {
            if (strstr($data, '-')) {
                $formato = 'AAAA-MM-DD';
            }

            if (strstr($data, ' ')) {
                list($data, $hora) = explode(' ', $data);
            }

            switch ($formato) {
                case 'DD-MM-AAAA': list($d, $m, $a) = explode('-', $data); break;
                case 'DD/MM/AAAA': list($d, $m, $a) = explode('/', $data); break;
                case 'AAAA/MM/DD': list($a, $m, $d) = explode('/', $data); break;
                case 'AAAA-MM-DD': list($a, $m, $d) = explode('-', $data); break;
                case 'AAAA/DD/MM': list($a, $d, $m) = explode('/', $data); break;
                case 'AAAA-DD-MM': list($a, $d, $m) = explode('-', $data); break;
                case 'MM-DD-AAAA': list($m, $d, $a) = explode('-', $data); break;
                case 'MM/DD/AAAA': list($m, $d, $a) = explode('/', $data); break;
                case 'AAAAMMDD': $a = substr($data, 0, 4); $m = substr($data, 4, 2); $d = substr($data, 6, 2); break;
                case 'AAAADDMM': $a = substr($data, 0, 4); $d = substr($data, 4, 2); $m = substr($data, 6, 2); break;
                default: return false; break;
            }

            if (checkdate($m, $d, $a)) {
                $return = $a . '-' . $m . '-' . $d;
                if (isset($hora)){
                    $return .= ' '. $hora;
                }
                return $return;
            } else {
                return false;
            }
        }
        return '';
    }

    /**
     * Método limpar propriedades
     *
     * @return $this
     */
    public function clear()
    {
        $this->_sql = null;
        $this->_sql_select = null;
        $this->_sql_from = null;
        $this->_sql_where = '';
        $this->_sql_order_by = null;
        $this->_sql_limit = null;
        $this->_sql_offset = null;

        if (is_array($this->fields)) {
            foreach ($this->fields as $k => $v) {
                $this->fields[$k]['value'] = null;
            }
        }

        $this->bindArrayToObject($this->_only_values());
        return $this;
    }

    private function clearInjection ($sql)
    {
        return str_ireplace(array('UNION', 'DROP ', 'CREATE ', 'ALTER ', 'OR 1=1', ), '', $sql);
    }

    /**
     * Método para definir o LIMIT
     *
     * @param $offset
     * @param $limit
     * @return $this
     */
    public function limit($offset, $limit)
    {
        $this->_sql_limit = " LIMIT " . $offset . ",". $limit;
        return $this;
    }

    /**
     * Método para chamar métodos de validações dinamicamente
     *
     * @param $classname
     * @param $method
     * @param $value
     * @return bool
     */
    public function validateMethods($classname, $method, $value)
    {
        if ($method && $value){
            if (method_exists($classname, $method)){
                return $this->{$method}($value);
                //return call_user_func($classname .'::' . $method, $value);
            }
        }
        return false;
    }
}