<?php

/**
 * Class Application_Model_DbConn
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_DbConn
{
   /*
    * Conexão com Banco de Daados MySQL
    * Parametros: Host, Username, Password, Database
    * Retorno: Instancia da conexão
    */
   public function connect($host, $username, $password, $database) {
      return new Zend_Db_Adapter_Pdo_Mysql(array(
                  'host' => $host,
                  'username' => $username,
                  'password' => $password,
                  'dbname' => $database
              ));
   }
}

