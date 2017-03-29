<?php
/*
 * Model: DbConn
 * Classe de conexão a banco de dados
 * Zend FrameWork
 * author: Carlos E Rizzo (carlos.rizzo@fsvas.com)
 * date: 08/01/2013
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

