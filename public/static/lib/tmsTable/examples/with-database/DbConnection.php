<?php

/**
 * Classe baseada em prepared statements para conexão e operações gerais no banco de dados
 *
 * @author Thiago Mallon 
 */
final class DbConnection {

    // variáveis dns do PDO
    private static $sgbd = 'mysql'; # seta SGBD
    private static $host = 'localhost'; # seta host
    private static $dbname = 'omundole_lenovo'; # seta nome do banco
    // variáveis para PDO
    private static $username = 'usuario'; # seta usuário do banco
    private static $password = 'senha'; # seta senha do banco
    // variáveis arquivo log de erros
    private static $filename = 'DbLog'; # seta arquivo para gravação de erros de conexão com o banco
    private static $fileMode = 'a'; # seta tipo de abertura para o banco
    private static $lineBreak = "\n"; # seta o tipo de quebra de linha
    private static $separating = '-------------------------------------------------'; # linha para separação de mensagens de erro

// método abre conexão

    private static function getConnection() {
        // verifica se é possível o estabelecimento de conexão
        try {
            $_dbh = new PDO(self::$sgbd . ":host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password);
            return $_dbh; # retorna instância de conexão
        } catch (PDOException $ex) { # captura (caso haja) exceção 
            self::writingErrorDbLog($ex->getMessage()); # caso seja gerada uma exceção, função é chamada
            return header('Location: error-page.php'); # retorna para página de erro
        }
    }

    // método que fecha a conexão
    private static function closeConnection($_dbh) {
        $_dbh = NULL; # atribui null à variável de conexão
    }

    // método que retorna resultado de tentativa de alteração em registro no banco
    static function executeUpdate($statement, $params) {
        $_dbh = self::getConnection(); # pega conxexão
        $sth = $_dbh->prepare($statement); # prepara o prepared statement
        $result = $sth->execute($params); # executa o prepared statement
        self::closeConnection($_dbh); # fecha conexão
        return $result; # retorna resultado da query
    }

    // método que retorna resultado de tentativa de inserção no banco
    static function executeInsert($statement, $params) {
        $_dbh = self::getConnection(); # pega conexão
        $sth = $_dbh->prepare($statement); # prepara o prepared statement
        $result = $sth->execute($params); # executa o prepared statement                
        self::closeConnection($_dbh); # fecha a conexão
        return $result; # retorna resultado da query
    }

    // método que retorna resultado de query de uma linha, em formato de array
    static function executeFetchRow($statement, $params = NULL) {
        $_dbh = self::getConnection(); # pega conexão
        $sth = $_dbh->prepare($statement); # prepara o prepared statement    
        $sth->execute($params); # executa o prepared statement
        while ($row = $sth->fetch()) { # navega resultados do prepared statement
            $partialResult[] = $row; # atribui resultados ao array
        }
        // verifica se houve de fato resultado, se sim, atribui o mesmo à variável, se não, atribui false à variável
        $resultArray = (isset($partialResult[0])) ? $partialResult[0] : false;
        self::closeConnection($_dbh); # fecha conexão
        return $resultArray; # retorna array de resultados
    }

    // método que retorna resultado de query de mais de uma linha, em formato de array
    static function executeFetchAll($statement, $params = NULL) {
        $_dbh = self::getConnection(); # pega conexão 
        $sth = $_dbh->prepare($statement); # prepara o prepared statement
        $sth->execute($params); # executa o prepared statement
        $resultArray = array(); # cria array vazio para armazenar resultados da query
        while ($row = $sth->fetch()) { # navega resultados do prepared statement
            $resultArray[] = $row; # atribui os resultados, caso hajam, ao array
        }
        self::closeConnection($_dbh); # fecha conexão
        return $resultArray; # retorna array de resultados
    }

    static function writingAcessLog($participante) {
        $statement = 'INSERT INTO log_acessos (str_ip, str_proxy, str_so, fk_id_usuario) VALUES (?,?,?,?)'; # monta o statement
        $userProxy = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : ''; # verifica se usuário está utilizando proxy
        $params = array($_SERVER['REMOTE_ADDR'], $userProxy, PHP_OS, $participante); # monta array de parametros para o statement
        $_dbh = self::getConnection(); # pega conexão 
        $sth = $_dbh->prepare($statement); # prepara o prepared statement
        $sth->execute($params); # executa o prepared statement
    }

    // método que cria a mensagem de erro 
    private static function composingErrorMessage($exceptionMsg) {
        $userProxy = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : ''; # verifica se usuário está utilizando proxy
        $errorMsg = 'Date: ' . date('Y-m-d - H:i:s') . self::$lineBreak . # pega data e hora atuais
                'User IP: ' . $_SERVER['REMOTE_ADDR'] . self::$lineBreak . # pega IP do usuário
                'User Proxy: ' . $userProxy . self::$lineBreak . # pega (caso exista) proxy do usuário
                'User Operational System: ' . PHP_OS . self::$lineBreak . # pega sistema operacional do usuário
                'Error: ' . $exceptionMsg . self::$lineBreak . # pega mensagem de erro
                self::$separating . self::$lineBreak; # adiciona linha 
        return $errorMsg; # retorna mensagem de erro montada
    }

    // método que salva no arquivo de erro a mensagem de erro
    private static function writingErrorDbLog($exceptionMsg) {
        $errorMsg = self::composingErrorMessage($exceptionMsg); # chama a função que cria a mensagem de erro, enviando e exceção        
        $fileHandle = fopen(self::$filename, self::$fileMode); # abre arquivo de erro        
        fwrite($fileHandle, $errorMsg); # escreve a mensagem de erro        
        fclose($fileHandle); # fecha arquivo de erro
    }

    // método que grava registros de possíveis ataques
    static function writingErrorDbInvasion($msg = NULL, $cpf = NULL, $referer = NULL, $file = NULL) {
        // monta statement
        $statement = 'INSERT INTO log_erros (str_ip, str_proxy, str_so, str_msg, num_cpf, str_referer, str_method, str_file) VALUES (?,?,?,?,?,?,?,?)';
        $userProxy = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : ''; # verifica se usuário está utilizando proxy
        // monta array de parâmetros para statement
        $params = array($_SERVER['REMOTE_ADDR'], $userProxy, PHP_OS, $msg, $cpf, $referer, __METHOD__, $file);
        $_dbh = self::getConnection(); # pega conexão 
        $sth = $_dbh->prepare($statement); # prepara o prepared statement
        // verifica se é possível gravar erro na tabela log_erros
        try {
            $sth->execute($params); # executa o prepared statement
        } catch (PDOException $ex) {
            self::writingErrorDbLog($ex->getMessage()); # caso seja gerada uma exceção, função é chamada
            return header('Location: error-page.php?msg=' . $ex->getMessage()); # retorna para página de erro
        }
    }

}

?>
