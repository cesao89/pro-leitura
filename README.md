# Plataforma Pró-Livro

#### Pré Requisitos
* PHP >= 5.5

##### PHP Config
* PDO
* MySQL
* MCrypt
* CUrl

___

Efetuar a cópia do arquivo 
***constants.php.default*** 
para a mesma pasta com o nome
***constants.php***
e configurar as informações em branco no 
mesmo tais como.

#### Conexão de Banco de Dados
Host, Usuário e Senha
```php
define("DB_HOST", "");
define("DB_USER", "");
define("DB_PASS", "");
```
___
#### Google Maps API
Chave gerada na criação de um novo projeto no 
Google Maps API.
```php
define("GOOGLE_MAPS_KEY", "");
```
___
#### Envio de E-mail
Configurações para o envio de e-mails. A pré 
configuração abaixo atende os envio pelo GMail 
alterando somente o usuário e senha.
```php
define("MAIL_HOST", "smtp.gmail.com");
define("MAIL_AUTH", true);
define("MAIL_USER", "SEU_USUARIO@gmail.com");
define("MAIL_PASS", "SUA_SENHA");
define("MAIL_SECURE", "ssl");
define("MAIL_PORT", 465);
define("MAIL_FROM", "SEU_USUARIO@gmail.com");
define("MAIL_FROM_NAME", "Plataforma Pro-Livro");
```