<?php

/**
 * Class Projeto
 *
 * @author Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_Projeto extends Application_Model_BaseDum
{
    public $db;
    public $fields = null;              // propriedades
    protected $classname = 'Application_Model_Projeto'; // nome da classe
    protected $table = 'projeto';       // tabela
    protected $pk = 'id';               // chave primária
    protected $auto_increment = true;   // autoincrement
    protected $db_config = array();     // config do acesso ao DB
    protected $config = array();        // config ( application.ini )
    private $conn;

    /**
     * Application_Model_Projeto constructor.
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
            'id' => array(
                'name' => 'id',
                'title' => 'ID',
                'type' => 'num',
                'required' => true,
                'form' => false
            ),
            'user_id' => array(
                'name' => 'user_id',
                'title' => 'ID do Usuário',
                'type' => 'num',
                'required' => true,
                'form' => false
            ),
            'status_id' => array(
                'name' => 'status_id',
                'title' => 'ID do Status',
                'type' => 'num',
                'required' => true,
                'form' => false
            ),
            'nome' => array(
                'name' => 'nome',
                'title' => 'Nome do Projeto',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'diferenciais_experiencia' => array(
                'name' => 'diferenciais_experiencia',
                'title' => 'Principais Diferenciais',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'vigencia_inicio' => array(
                'name' => 'vigencia_inicio',
                'title' => 'Vigência do Projeto (Inicio)',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'vigencia_fim' => array(
                'name' => 'vigencia_fim',
                'title' => 'Vigência do Projeto (Fim)',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'natureza' => array(
                'name' => 'natureza',
                'title' => 'Natureza do Projeto',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'publico_atendido' => array(
                'name' => 'publico_atendido',
                'title' => 'Publico Atendido',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'faixa_etaria' => array(
                'name' => 'faixa_etaria',
                'title' => 'Faixa Etária',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'genero' => array(
                'name' => 'genero',
                'title' => 'Gênero',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'atendidos_total' => array(
                'name' => 'atendidos_total',
                'title' => 'Atendidos Total',
                'type' => 'num',
                'required' => false,
                'form' => false
            ),
            'atendidos_ultimo_ano' => array(
                'name' => 'atendidos_ultimo_ano',
                'title' => 'Atendidos no Ultimo Ano',
                'type' => 'num',
                'required' => false,
                'form' => false
            ),
            'atendidos_por_acao' => array(
                'name' => 'atendidos_por_acao',
                'title' => 'Atendidos por Ação/Evento',
                'type' => 'num',
                'required' => false,
                'form' => false
            ),
            'atendidos_detalhes' => array(
                'name' => 'atendidos_detalhes',
                'title' => 'Detalhes dos Atendidos',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'localizacao_territorio' => array(
                'name' => 'localizacao_territorio',
                'title' => 'Território',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'localizacao_regional' => array(
                'name' => 'localizacao_regional',
                'title' => 'Regional',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'localizacao_estado' => array(
                'name' => 'localizacao_estado',
                'title' => 'Estado',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'localizacao_cidade' => array(
                'name' => 'localizacao_cidade',
                'title' => 'Cidade',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'localizacao_outro' => array(
                'name' => 'localizacao_outro',
                'title' => 'Detalhes da Localização',
                'type' => 'char',
                'required' => false,
                'form' => false
            ),
            'organizacao_nome' => array(
                'name' => 'organizacao_nome',
                'title' => 'Nome da Organização',
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

    public function getFullProject($id)
    {
        $sqlProjeto = "SELECT * FROM `proleitura`.`projeto` WHERE `id`='". $id ."' LIMIT 1";
        $fetchProjeto = parent::free_select($sqlProjeto);
        $projectInfo = array(
            'id'                        => (isset($fetchProjeto[0]->id) && !empty($fetchProjeto[0]->id)) ? $fetchProjeto[0]->id : null,
            'user_id'                   => (isset($fetchProjeto[0]->user_id) && !empty($fetchProjeto[0]->user_id)) ? $fetchProjeto[0]->user_id : null,
            'status_id'                   => (isset($fetchProjeto[0]->status_id) && !empty($fetchProjeto[0]->status_id)) ? $fetchProjeto[0]->status_id : null,
            'nome'                      => (isset($fetchProjeto[0]->nome) && !empty($fetchProjeto[0]->nome)) ? $fetchProjeto[0]->nome : null,
            'diferenciais_experiencia'  => (isset($fetchProjeto[0]->diferenciais_experiencia) && !empty($fetchProjeto[0]->diferenciais_experiencia)) ? $fetchProjeto[0]->diferenciais_experiencia : null,
            'vigencia_inicio'           => (isset($fetchProjeto[0]->vigencia_inicio) && !empty($fetchProjeto[0]->vigencia_inicio) && $fetchProjeto[0]->vigencia_inicio != '0000-00-00') ? $this->dateFormat($fetchProjeto[0]->vigencia_inicio, 'm/Y') : null,
            'vigencia_fim'              => (isset($fetchProjeto[0]->vigencia_fim) && !empty($fetchProjeto[0]->vigencia_fim) && $fetchProjeto[0]->vigencia_fim != '0000-00-00') ? $this->dateFormat($fetchProjeto[0]->vigencia_fim, 'm/Y') : null,
            'natureza'                  => (isset($fetchProjeto[0]->natureza) && !empty($fetchProjeto[0]->natureza)) ? explode(',', $fetchProjeto[0]->natureza) : null,
            'publico_atendido'          => (isset($fetchProjeto[0]->publico_atendido) && !empty($fetchProjeto[0]->publico_atendido)) ? explode(',', $fetchProjeto[0]->publico_atendido) : null,
            'faixa_etaria'              => (isset($fetchProjeto[0]->faixa_etaria) && !empty($fetchProjeto[0]->faixa_etaria)) ? $fetchProjeto[0]->faixa_etaria : null,
            'gereno'                    => (isset($fetchProjeto[0]->gereno) && !empty($fetchProjeto[0]->gereno)) ? $fetchProjeto[0]->gereno : null,
            'atendidos_total'           => (isset($fetchProjeto[0]->atendidos_total) && !empty($fetchProjeto[0]->atendidos_total)) ? $fetchProjeto[0]->atendidos_total : 0,
            'atendidos_ultimo_ano'      => (isset($fetchProjeto[0]->atendidos_ultimo_ano) && !empty($fetchProjeto[0]->atendidos_ultimo_ano)) ? $fetchProjeto[0]->atendidos_ultimo_ano : 0,
            'atendidos_por_acao'        => (isset($fetchProjeto[0]->atendidos_por_acao) && !empty($fetchProjeto[0]->atendidos_por_acao)) ? $fetchProjeto[0]->atendidos_por_acao : 0,
            'atendidos_detalhes'        => (isset($fetchProjeto[0]->atendidos_detalhes) && !empty($fetchProjeto[0]->atendidos_detalhes)) ? $fetchProjeto[0]->atendidos_detalhes : null,
            'localizacao_territorio'    => (isset($fetchProjeto[0]->localizacao_territorio) && !empty($fetchProjeto[0]->localizacao_territorio)) ? explode(',', $fetchProjeto[0]->localizacao_territorio) : null,
            'localizacao_regional'      => (isset($fetchProjeto[0]->localizacao_regional) && !empty($fetchProjeto[0]->localizacao_regional)) ? $fetchProjeto[0]->localizacao_regional : null,
            'localizacao_estado'        => (isset($fetchProjeto[0]->localizacao_estado) && !empty($fetchProjeto[0]->localizacao_estado)) ? explode(',', $fetchProjeto[0]->localizacao_estado) : null,
            'localizacao_cidade'        => (isset($fetchProjeto[0]->localizacao_cidade) && !empty($fetchProjeto[0]->localizacao_cidade)) ? explode(',', $fetchProjeto[0]->localizacao_cidade) : null,
            'localizacao_outro'         => (isset($fetchProjeto[0]->localizacao_outro) && !empty($fetchProjeto[0]->localizacao_outro)) ? $fetchProjeto[0]->localizacao_outro : null,
            'organizacao_nome'          => (isset($fetchProjeto[0]->organizacao_nome) && !empty($fetchProjeto[0]->organizacao_nome)) ? $fetchProjeto[0]->organizacao_nome : null,
            'created_at'                => (isset($fetchProjeto[0]->created_at) && !empty($fetchProjeto[0]->created_at)) ? $fetchProjeto[0]->created_at : null,
            'updated_at'                => (isset($fetchProjeto[0]->updated_at) && !empty($fetchProjeto[0]->updated_at)) ? $fetchProjeto[0]->updated_at : null,
        );
        $project = json_decode(json_encode($projectInfo), false);

        $sqlProjetoExpectativa = "SELECT * FROM `proleitura`.`projeto_expectativa` WHERE `project_id`='". $id ."' LIMIT 10";
        $fetchProjetoExpectativa = parent::free_select($sqlProjetoExpectativa);
        $projectExpectancy = array();
        foreach ($fetchProjetoExpectativa as $value){
            $projectExpectancy[] = array(
                'project_id'    => (isset($value->project_id) && !empty($value->project_id)) ? $value->project_id : null,
                'expectativa'   => (isset($value->expectativa) && !empty($value->expectativa)) ? ($value->expectativa) : null,
                'detalhe'       => (isset($value->detalhe) && !empty($value->detalhe)) ? ($value->detalhe) : null,
            );
        }
        $project->projeto_expectativa = json_decode(json_encode($projectExpectancy), false);

        $sqlOrganizacaoCategoria = "SELECT * FROM `proleitura`.`projeto_categorias` WHERE `project_id`='". $id ."' LIMIT 10";
        $fetchOrganizacaoCategoria = parent::free_select($sqlOrganizacaoCategoria);
        $organizationCategory = array();
        foreach ($fetchOrganizacaoCategoria as $value){
            $organizationCategory[] = array(
                'project_id'    => (isset($value->project_id) && !empty($value->project_id)) ? $value->project_id : null,
                'categoria'     => (isset($value->categoria) && !empty($value->categoria)) ? ($value->categoria) : null,
                'detalhe'       => (isset($value->detalhe) && !empty($value->detalhe)) ? ($value->detalhe) : null,
            );
        }
        $project->organizacao_categoria = json_decode(json_encode($organizationCategory), false);

        $sqlOrganizacaoParceiros = "SELECT * FROM `proleitura`.`projeto_parceiros` WHERE `project_id`='". $id ."' LIMIT 5";
        $fetchOrganizacaoParceiros = parent::free_select($sqlOrganizacaoParceiros);
        $organizationPartner= array(
            'project_id'            => (isset($fetchOrganizacaoParceiros[0]->project_id) && !empty($fetchOrganizacaoParceiros[0]->project_id)) ? $fetchOrganizacaoParceiros[0]->project_id : null,
            'patrocinio'            => (isset($fetchOrganizacaoParceiros[0]->patrocinio) && !empty($fetchOrganizacaoParceiros[0]->patrocinio)) ? ($fetchOrganizacaoParceiros[0]->patrocinio) : null,
            'patrocinio_percentual' => (isset($fetchOrganizacaoParceiros[0]->patrocinio_percentual) && !empty($fetchOrganizacaoParceiros[0]->patrocinio_percentual)) ? $fetchOrganizacaoParceiros[0]->patrocinio_percentual : 0,
            'apoio_tecnico'         => (isset($fetchOrganizacaoParceiros[0]->apoio_tecnico) && !empty($fetchOrganizacaoParceiros[0]->apoio_tecnico)) ? ($fetchOrganizacaoParceiros[0]->apoio_tecnico) : null,
            'apoio_institucional'   => (isset($fetchOrganizacaoParceiros[0]->apoio_institucional) && !empty($fetchOrganizacaoParceiros[0]->apoio_institucional)) ? ($fetchOrganizacaoParceiros[0]->apoio_institucional) : null,
            'outros'                => (isset($fetchOrganizacaoParceiros[0]->outros) && !empty($fetchOrganizacaoParceiros[0]->outros)) ? ($fetchOrganizacaoParceiros[0]->outros) : null,
        );
        $project->organizacao_parceiros = json_decode(json_encode($organizationPartner), false);

        $sqlProjetoEquipe = "SELECT * FROM `proleitura`.`projeto_equipe` WHERE `project_id`='". $id ."' LIMIT 10";
        $fetchProjetoEquipe = parent::free_select($sqlProjetoEquipe);
        $projectTeam = array();
        foreach ($fetchProjetoEquipe as $value){
            $projectTeam[] = array(
                'project_id'    => (isset($value->project_id) && !empty($value->project_id)) ? $value->project_id : null,
                'quantidade'    => (isset($value->quantidade) && !empty($value->quantidade)) ? ($value->quantidade) : null,
                'equipe'        => (isset($value->equipe) && !empty($value->equipe)) ? ($value->equipe) : null,
                'detalhe'       => (isset($value->detalhe) && !empty($value->detalhe)) ? ($value->detalhe) : null,
            );
        }
        $project->project_team = json_decode(json_encode($projectTeam), false);

        $sqlProjetoDetalhes = "SELECT * FROM `proleitura`.`projeto_detalhes` WHERE `project_id`='". $id ."' LIMIT 1";
        $fetchProjetoDetalhes = parent::free_select($sqlProjetoDetalhes);

        $sqlProjetoMaisDetalhes = "SELECT * FROM `proleitura`.`projeto_mais_detalhes` WHERE `project_id`='". $id ."' LIMIT 1";
        $fetchProjetoMaisDetalhes = parent::free_select($sqlProjetoMaisDetalhes);
        $projectDetails = array(
            'project_id'                => (isset($fetchProjetoDetalhes[0]->project_id) && !empty($fetchProjetoDetalhes[0]->project_id)) ? $fetchProjetoDetalhes[0]->project_id : null,
            'sintese'                   => (isset($fetchProjetoDetalhes[0]->sintese) && !empty($fetchProjetoDetalhes[0]->sintese)) ? ($fetchProjetoDetalhes[0]->sintese) : null,
            'caracteristicas'           => (isset($fetchProjetoDetalhes[0]->caracteristicas) && !empty($fetchProjetoDetalhes[0]->caracteristicas)) ? ($fetchProjetoDetalhes[0]->caracteristicas) : null,
            'objetivos'                 => (isset($fetchProjetoDetalhes[0]->objetivos) && !empty($fetchProjetoDetalhes[0]->objetivos)) ? ($fetchProjetoDetalhes[0]->objetivos) : null,
            'justificativas'            => (isset($fetchProjetoDetalhes[0]->justificativas) && !empty($fetchProjetoDetalhes[0]->justificativas)) ? ($fetchProjetoDetalhes[0]->justificativas) : null,
            'metodologia_a'             => (isset($fetchProjetoDetalhes[0]->metodologia_a) && !empty($fetchProjetoDetalhes[0]->metodologia_a)) ? ($fetchProjetoDetalhes[0]->metodologia_a) : null,
            'metodologia_b'             => (isset($fetchProjetoDetalhes[0]->metodologia_b) && !empty($fetchProjetoDetalhes[0]->metodologia_b)) ? ($fetchProjetoDetalhes[0]->metodologia_b) : null,
            'resultado'                 => (isset($fetchProjetoDetalhes[0]->resultado) && !empty($fetchProjetoDetalhes[0]->resultado)) ? ($fetchProjetoDetalhes[0]->resultado) : null,
            'avaliacoes'                => (isset($fetchProjetoMaisDetalhes[0]->avaliacoes) && !empty($fetchProjetoMaisDetalhes[0]->avaliacoes)) ? ($fetchProjetoMaisDetalhes[0]->avaliacoes) : null,
            'depoimentos'               => (isset($fetchProjetoMaisDetalhes[0]->depoimentos) && !empty($fetchProjetoMaisDetalhes[0]->depoimentos)) ? ($fetchProjetoMaisDetalhes[0]->depoimentos) : null,
            'premios'                   => (isset($fetchProjetoMaisDetalhes[0]->premios) && !empty($fetchProjetoMaisDetalhes[0]->premios)) ? ($fetchProjetoMaisDetalhes[0]->premios) : null,
            'principais_dificuldades'   => (isset($fetchProjetoMaisDetalhes[0]->principais_dificuldades) && !empty($fetchProjetoMaisDetalhes[0]->principais_dificuldades)) ? ($fetchProjetoMaisDetalhes[0]->principais_dificuldades) : null,
            'dificuldades_superadas'    => (isset($fetchProjetoMaisDetalhes[0]->dificuldades_superadas) && !empty($fetchProjetoMaisDetalhes[0]->dificuldades_superadas)) ? ($fetchProjetoMaisDetalhes[0]->dificuldades_superadas) : null,
            'garantir_continuidade'     => (isset($fetchProjetoMaisDetalhes[0]->garantir_continuidade) && !empty($fetchProjetoMaisDetalhes[0]->garantir_continuidade)) ? ($fetchProjetoMaisDetalhes[0]->garantir_continuidade) : null,
            'site'                      => (isset($fetchProjetoMaisDetalhes[0]->site) && !empty($fetchProjetoMaisDetalhes[0]->site)) ? ($fetchProjetoMaisDetalhes[0]->site) : null,
            'redes_sociais'             => (isset($fetchProjetoMaisDetalhes[0]->redes_sociais) && !empty($fetchProjetoMaisDetalhes[0]->redes_sociais)) ? ($fetchProjetoMaisDetalhes[0]->redes_sociais) : null,
            'fotos_videos'              => (isset($fetchProjetoMaisDetalhes[0]->fotos_videos) && !empty($fetchProjetoMaisDetalhes[0]->fotos_videos)) ? ($fetchProjetoMaisDetalhes[0]->fotos_videos) : null,
            'adicional'                 => (isset($fetchProjetoMaisDetalhes[0]->adicional) && !empty($fetchProjetoMaisDetalhes[0]->adicional)) ? ($fetchProjetoMaisDetalhes[0]->adicional) : null,
        );
        $project->project_details = json_decode(json_encode($projectDetails), false);

        $sqlProjetoResponsavel = "SELECT * FROM `proleitura`.`projeto_responsavel` WHERE `project_id`='". $id ."' LIMIT 1";
        $fetchProjetoResponsavel = parent::free_select($sqlProjetoResponsavel);
        $projectResponsible = array(
            'project_id'                => (isset($fetchProjetoResponsavel[0]->project_id) && !empty($fetchProjetoResponsavel[0]->project_id)) ? $fetchProjetoResponsavel[0]->project_id : null,
            'organizacao'               => (isset($fetchProjetoResponsavel[0]->organizacao) && !empty($fetchProjetoResponsavel[0]->organizacao)) ? ($fetchProjetoResponsavel[0]->organizacao) : null,
            'cnpj'                      => (isset($fetchProjetoResponsavel[0]->cnpj) && !empty($fetchProjetoResponsavel[0]->cnpj)) ? $this->mask($fetchProjetoResponsavel[0]->cnpj, '##.###.###/####-##') : null,
            'cidade'                    => (isset($fetchProjetoResponsavel[0]->cidade) && !empty($fetchProjetoResponsavel[0]->cidade)) ? ($fetchProjetoResponsavel[0]->cidade) : null,
            'uf'                        => (isset($fetchProjetoResponsavel[0]->uf) && !empty($fetchProjetoResponsavel[0]->uf)) ? ($fetchProjetoResponsavel[0]->uf) : null,
            'cep'                       => (isset($fetchProjetoResponsavel[0]->cep) && !empty($fetchProjetoResponsavel[0]->cep)) ? $this->mask($fetchProjetoResponsavel[0]->cep, '#####-###') : null,
            'email'                     => (isset($fetchProjetoResponsavel[0]->email) && !empty($fetchProjetoResponsavel[0]->email)) ? ($fetchProjetoResponsavel[0]->email) : null,
            'telefone'                  => (isset($fetchProjetoResponsavel[0]->telefone) && !empty($fetchProjetoResponsavel[0]->telefone)) ? $this->mask($fetchProjetoResponsavel[0]->telefone, (strlen($fetchProjetoResponsavel[0]->telefone) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'celular'                   => (isset($fetchProjetoResponsavel[0]->celular) && !empty($fetchProjetoResponsavel[0]->celular)) ? $this->mask($fetchProjetoResponsavel[0]->celular, (strlen($fetchProjetoResponsavel[0]->celular) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'site'                      => (isset($fetchProjetoResponsavel[0]->site) && !empty($fetchProjetoResponsavel[0]->site)) ? ($fetchProjetoResponsavel[0]->site) : null,
            'facebook'                  => (isset($fetchProjetoResponsavel[0]->facebook) && !empty($fetchProjetoResponsavel[0]->facebook)) ? ($fetchProjetoResponsavel[0]->facebook) : null,
            'outros_contatos'           => (isset($fetchProjetoResponsavel[0]->outros_contatos) && !empty($fetchProjetoResponsavel[0]->outros_contatos)) ? ($fetchProjetoResponsavel[0]->outros_contatos) : null,
            'pessoa_responsavel'        => (isset($fetchProjetoResponsavel[0]->pessoa_responsavel) && !empty($fetchProjetoResponsavel[0]->pessoa_responsavel)) ? ($fetchProjetoResponsavel[0]->pessoa_responsavel) : null,
            'pessoa_cargo'              => (isset($fetchProjetoResponsavel[0]->pessoa_cargo) && !empty($fetchProjetoResponsavel[0]->pessoa_cargo)) ? ($fetchProjetoResponsavel[0]->pessoa_cargo) : null,
            'pessoa_email'              => (isset($fetchProjetoResponsavel[0]->pessoa_email) && !empty($fetchProjetoResponsavel[0]->pessoa_email)) ? ($fetchProjetoResponsavel[0]->pessoa_email) : null,
            'pessoa_telefone'           => (isset($fetchProjetoResponsavel[0]->pessoa_telefone) && !empty($fetchProjetoResponsavel[0]->pessoa_telefone)) ? $this->mask($fetchProjetoResponsavel[0]->pessoa_telefone, (strlen($fetchProjetoResponsavel[0]->pessoa_telefone) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'pessoa_celular'            => (isset($fetchProjetoResponsavel[0]->pessoa_celular) && !empty($fetchProjetoResponsavel[0]->pessoa_celular)) ? $this->mask($fetchProjetoResponsavel[0]->pessoa_celular, (strlen($fetchProjetoResponsavel[0]->pessoa_celular) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'pessoa_outros_contatos'    => (isset($fetchProjetoResponsavel[0]->pessoa_outros_contatos) && !empty($fetchProjetoResponsavel[0]->pessoa_outros_contatos)) ? ($fetchProjetoResponsavel[0]->pessoa_outros_contatos) : null,
        );
        $project->project_responsible = json_decode(json_encode($projectResponsible), false);

        return $project;
    }

    public function lastProjects($user_id, $limit=3)
    {
        $sqlProjeto = "SELECT 
                `proleitura`.`projeto`.`id` AS p_id
                , `proleitura`.`projeto_status`.`status` AS p_status
                , `proleitura`.`projeto`.`nome` AS p_nome
                , `proleitura`.`projeto`.`updated_at` AS p_updated
                , `proleitura`.`projeto_detalhes`.`sintese` AS p_sintese
            FROM `proleitura`.`projeto`
                JOIN `proleitura`.`projeto_status` ON `proleitura`.`projeto_status`.`id`=`proleitura`.`projeto`.`status_id`
                LEFT JOIN `proleitura`.`projeto_detalhes` ON `proleitura`.`projeto_detalhes`.`project_id`=`proleitura`.`projeto`.`id`
            WHERE `proleitura`.`projeto`.`user_id`='". $user_id ."' 
            LIMIT ". $limit;
        $fetchProjeto = parent::free_select($sqlProjeto);
        $project = array();
        foreach ($fetchProjeto as $projeto){
            $project[] = array(
                'id'            => (isset($projeto->p_id) && !empty($projeto->p_id)) ? $projeto->p_id : null,
                'status'        => (isset($projeto->p_status) && !empty($projeto->p_status)) ? $projeto->p_status : null,
                'nome'          => (isset($projeto->p_nome) && !empty($projeto->p_nome)) ? $projeto->p_nome : null,
                'updated_at'    => (isset($projeto->p_updated) && !empty($projeto->p_updated)) ? $this->dateFormat($projeto->p_updated, 'd/m/Y H:i:s') : null,
                'sintese'       => (isset($projeto->p_sintese) && !empty($projeto->p_sintese)) ? $projeto->p_sintese: null
            );
        }
        return $project;
    }

    public function listProject($where, $limit)
    {
        $projetoFetch = $this->where($where)->limit(0, $limit)->order_by('-id')->filter();

        $projects = array();
        foreach ($projetoFetch as $project){
            $projetoStatusModel = new Application_Model_ProjetoStatus($project->status_id);
            $projects[] = array(
                'id'                => (isset($project->id) && !empty($project->id)) ? $project->id : null,
                'user_id'           => (isset($project->user_id) && !empty($project->user_id)) ? $project->user_id : null,
                'nome'              => (isset($project->nome) && !empty($project->nome)) ? $project->nome : null,
                'territorio'        => (isset($project->localizacao_territorio) && !empty($project->localizacao_territorio)) ? $project->localizacao_territorio : null,
                'regional'          => (isset($project->localizacao_regional) && !empty($project->localizacao_regional)) ? $project->localizacao_regional : null,
                'estado'            => (isset($project->localizacao_estado) && !empty($project->localizacao_estado)) ? $project->localizacao_estado : null,
                'cidade'            => (isset($project->localizacao_cidade) && !empty($project->localizacao_cidade)) ? $project->localizacao_cidade : null,
                'vigencia_inicio'   => (isset($project->vigencia_inicio) && !empty($project->vigencia_inicio)) ? $project->vigencia_inicio : null,
                'vigencia_fim'      => (isset($project->vigencia_fim) && !empty($project->vigencia_fim)) ? $project->vigencia_fim : null,
                'status'            => $projetoStatusModel->status,
            );
        }

        return $projects;
    }

    private function dateFormat($dateIn, $formatOut='d/m/Y')
    {
        if(!$dateIn)
            return $dateIn;

        $newDate = new DateTime($dateIn);
        return $newDate->format($formatOut);
    }

    private function mask($val, $mask)
    {
        if(empty($val) || empty($mask))
            return $val;

        $needMask = preg_replace('/[^#]/', '', $mask);
        $maskared = '';
        $k = 0;

        if(strlen($needMask) != strlen($val))
            $val = str_pad($val, strlen($needMask), 0, STR_PAD_LEFT);

        for($i=0; $i <= strlen($mask)-1; $i++){
            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
}