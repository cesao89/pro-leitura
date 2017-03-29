<?php

/**
 * Class Projeto
 *
 * @author: Cesar O Domingos <cesar_web@live.com>
 */
class Application_Model_Projeto extends Application_Model_BaseDum
{
    private $conn;

    /**
     * Projeto constructor.
     */
    public function __construct()
    {
        // Pega valores do application.ini
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);

        // Define conexão com DB
        $this->conn = parent::dbConn(
            $config->resources->multidb->proleitura->host,
            $config->resources->multidb->proleitura->username,
            $config->resources->multidb->proleitura->password,
            $config->resources->multidb->proleitura->dbname
        );
    }

    public function getFullProject($id)
    {
        $sqlProjeto = "SELECT * FROM `proleitura`.`projeto` WHERE `id`='". $id ."' LIMIT 1";
        $fetchProjeto = parent::free_select($sqlProjeto);
        $projectInfo = array(
            'id'                        => (isset($fetchProjeto[0]->id) && !empty($fetchProjeto[0]->id)) ? $fetchProjeto[0]->id : null,
            'user_id'                   => (isset($fetchProjeto[0]->user_id) && !empty($fetchProjeto[0]->user_id)) ? $fetchProjeto[0]->user_id : null,
            'nome'                      => (isset($fetchProjeto[0]->nome) && !empty($fetchProjeto[0]->nome)) ? utf8_encode($fetchProjeto[0]->nome) : null,
            'diferenciais_experiencia'  => (isset($fetchProjeto[0]->diferenciais_experiencia) && !empty($fetchProjeto[0]->diferenciais_experiencia)) ? utf8_encode($fetchProjeto[0]->diferenciais_experiencia) : null,
            'vigencia_inicio'           => (isset($fetchProjeto[0]->vigencia_inicio) && !empty($fetchProjeto[0]->vigencia_inicio)) ? $this->dateFormat($fetchProjeto[0]->vigencia_inicio, 'm/Y') : null,
            'vigencia_fim'              => (isset($fetchProjeto[0]->vigencia_fim) && !empty($fetchProjeto[0]->vigencia_fim)) ? $this->dateFormat($fetchProjeto[0]->vigencia_fim, 'm/Y') : null,
            'natureza'                  => (isset($fetchProjeto[0]->natureza) && !empty($fetchProjeto[0]->natureza)) ? utf8_encode($fetchProjeto[0]->natureza) : null,
            'publico_atendido'          => (isset($fetchProjeto[0]->publico_atendido) && !empty($fetchProjeto[0]->publico_atendido)) ? utf8_encode($fetchProjeto[0]->publico_atendido) : null,
            'faixa_etaria'              => (isset($fetchProjeto[0]->faixa_etaria) && !empty($fetchProjeto[0]->faixa_etaria)) ? utf8_encode($fetchProjeto[0]->faixa_etaria) : null,
            'gereno'                    => (isset($fetchProjeto[0]->gereno) && !empty($fetchProjeto[0]->gereno)) ? utf8_encode($fetchProjeto[0]->gereno) : null,
            'atendidos_total'           => (isset($fetchProjeto[0]->atendidos_total) && !empty($fetchProjeto[0]->atendidos_total)) ? $fetchProjeto[0]->atendidos_total : 0,
            'atendidos_ultimo_ano'      => (isset($fetchProjeto[0]->atendidos_ultimo_ano) && !empty($fetchProjeto[0]->atendidos_ultimo_ano)) ? $fetchProjeto[0]->atendidos_ultimo_ano : 0,
            'atendidos_por_acao'        => (isset($fetchProjeto[0]->atendidos_por_acao) && !empty($fetchProjeto[0]->atendidos_por_acao)) ? $fetchProjeto[0]->atendidos_por_acao : 0,
            'atendidos_detalhes'        => (isset($fetchProjeto[0]->atendidos_detalhes) && !empty($fetchProjeto[0]->atendidos_detalhes)) ? utf8_encode($fetchProjeto[0]->atendidos_detalhes) : null,
            'localizacao_territorio'    => (isset($fetchProjeto[0]->localizacao_territorio) && !empty($fetchProjeto[0]->localizacao_territorio)) ? utf8_encode($fetchProjeto[0]->localizacao_territorio) : null,
            'localizacao_regional'      => (isset($fetchProjeto[0]->localizacao_regional) && !empty($fetchProjeto[0]->localizacao_regional)) ? utf8_encode($fetchProjeto[0]->localizacao_regional) : null,
            'localizacao_estado'        => (isset($fetchProjeto[0]->localizacao_estado) && !empty($fetchProjeto[0]->localizacao_estado)) ? explode(',', utf8_encode($fetchProjeto[0]->localizacao_estado)) : null,
            'localizacao_cidade'        => (isset($fetchProjeto[0]->localizacao_cidade) && !empty($fetchProjeto[0]->localizacao_cidade)) ? explode(',', utf8_encode($fetchProjeto[0]->localizacao_cidade)) : null,
            'localizacao_outro'         => (isset($fetchProjeto[0]->localizacao_outro) && !empty($fetchProjeto[0]->localizacao_outro)) ? utf8_encode($fetchProjeto[0]->localizacao_outro) : null,
            'organizacao_nome'          => (isset($fetchProjeto[0]->organizacao_nome) && !empty($fetchProjeto[0]->organizacao_nome)) ? utf8_encode($fetchProjeto[0]->organizacao_nome) : null,
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
                'expectativa'   => (isset($value->expectativa) && !empty($value->expectativa)) ? utf8_encode($value->expectativa) : null,
                'detalhe'       => (isset($value->detalhe) && !empty($value->detalhe)) ? utf8_encode($value->detalhe) : null,
            );
        }
        $project->projeto_expectativa = json_decode(json_encode($projectExpectancy), false);

        $sqlOrganizacaoCategoria = "SELECT * FROM `proleitura`.`organizacao_categoria` WHERE `project_id`='". $id ."' LIMIT 10";
        $fetchOrganizacaoCategoria = parent::free_select($sqlOrganizacaoCategoria);
        $organizationCategory = array();
        foreach ($fetchOrganizacaoCategoria as $value){
            $organizationCategory[] = array(
                'project_id'    => (isset($value->project_id) && !empty($value->project_id)) ? $value->project_id : null,
                'categoria'     => (isset($value->categoria) && !empty($value->categoria)) ? utf8_encode($value->categoria) : null,
                'detalhe'       => (isset($value->detalhe) && !empty($value->detalhe)) ? utf8_encode($value->detalhe) : null,
            );
        }
        $project->organizacao_categoria = json_decode(json_encode($organizationCategory), false);

        $sqlOrganizacaoParceiros = "SELECT * FROM `proleitura`.`organizacao_parceiros` WHERE `project_id`='". $id ."' LIMIT 5";
        $fetchOrganizacaoParceiros = parent::free_select($sqlOrganizacaoParceiros);
        $organizationPartner= array(
            'project_id'            => (isset($fetchOrganizacaoParceiros[0]->project_id) && !empty($fetchOrganizacaoParceiros[0]->project_id)) ? $fetchOrganizacaoParceiros[0]->project_id : null,
            'patrocinio'            => (isset($fetchOrganizacaoParceiros[0]->patrocinio) && !empty($fetchOrganizacaoParceiros[0]->patrocinio)) ? utf8_encode($fetchOrganizacaoParceiros[0]->patrocinio) : null,
            'patrocinio_percentual' => (isset($fetchOrganizacaoParceiros[0]->patrocinio_percentual) && !empty($fetchOrganizacaoParceiros[0]->patrocinio_percentual)) ? $fetchOrganizacaoParceiros[0]->patrocinio_percentual : 0,
            'apoio_tecnico'         => (isset($fetchOrganizacaoParceiros[0]->apoio_tecnico) && !empty($fetchOrganizacaoParceiros[0]->apoio_tecnico)) ? utf8_encode($fetchOrganizacaoParceiros[0]->apoio_tecnico) : null,
            'apoio_institucional'   => (isset($fetchOrganizacaoParceiros[0]->apoio_institucional) && !empty($fetchOrganizacaoParceiros[0]->apoio_institucional)) ? utf8_encode($fetchOrganizacaoParceiros[0]->apoio_institucional) : null,
            'outros'                => (isset($fetchOrganizacaoParceiros[0]->outros) && !empty($fetchOrganizacaoParceiros[0]->outros)) ? utf8_encode($fetchOrganizacaoParceiros[0]->outros) : null,
        );
        $project->organizacao_parceiros = json_decode(json_encode($organizationPartner), false);

        $sqlProjetoEquipe = "SELECT * FROM `proleitura`.`projeto_equipe` WHERE `project_id`='". $id ."' LIMIT 10";
        $fetchProjetoEquipe = parent::free_select($sqlProjetoEquipe);
        $projectTeam = array();
        foreach ($fetchProjetoEquipe as $value){
            $projectTeam[] = array(
                'project_id'    => (isset($value->project_id) && !empty($value->project_id)) ? $value->project_id : null,
                'quantidade'    => (isset($value->quantidade) && !empty($value->quantidade)) ? utf8_encode($value->quantidade) : null,
                'equipe'        => (isset($value->equipe) && !empty($value->equipe)) ? utf8_encode($value->equipe) : null,
                'detalhe'       => (isset($value->detalhe) && !empty($value->detalhe)) ? utf8_encode($value->detalhe) : null,
            );
        }
        $project->project_team = json_decode(json_encode($projectTeam), false);

        $sqlProjetoDetalhes = "SELECT * FROM `proleitura`.`projeto_detalhes` WHERE `project_id`='". $id ."' LIMIT 1";
        $fetchProjetoDetalhes = parent::free_select($sqlProjetoDetalhes);

        $sqlProjetoMaisDetalhes = "SELECT * FROM `proleitura`.`projeto_mais_detalhes` WHERE `project_id`='". $id ."' LIMIT 1";
        $fetchProjetoMaisDetalhes = parent::free_select($sqlProjetoMaisDetalhes);
        $projectDetails = array(
            'project_id'                => (isset($fetchProjetoDetalhes[0]->project_id) && !empty($fetchProjetoDetalhes[0]->project_id)) ? $fetchProjetoDetalhes[0]->project_id : null,
            'sintese'                   => (isset($fetchProjetoDetalhes[0]->sintese) && !empty($fetchProjetoDetalhes[0]->sintese)) ? utf8_encode($fetchProjetoDetalhes[0]->sintese) : null,
            'caracteristicas'           => (isset($fetchProjetoDetalhes[0]->caracteristicas) && !empty($fetchProjetoDetalhes[0]->caracteristicas)) ? utf8_encode($fetchProjetoDetalhes[0]->caracteristicas) : null,
            'objetivos'                 => (isset($fetchProjetoDetalhes[0]->objetivos) && !empty($fetchProjetoDetalhes[0]->objetivos)) ? utf8_encode($fetchProjetoDetalhes[0]->objetivos) : null,
            'justificativas'            => (isset($fetchProjetoDetalhes[0]->justificativas) && !empty($fetchProjetoDetalhes[0]->justificativas)) ? utf8_encode($fetchProjetoDetalhes[0]->justificativas) : null,
            'metodologia_a'             => (isset($fetchProjetoDetalhes[0]->metodologia_a) && !empty($fetchProjetoDetalhes[0]->metodologia_a)) ? utf8_encode($fetchProjetoDetalhes[0]->metodologia_a) : null,
            'metodologia_b'             => (isset($fetchProjetoDetalhes[0]->metodologia_b) && !empty($fetchProjetoDetalhes[0]->metodologia_b)) ? utf8_encode($fetchProjetoDetalhes[0]->metodologia_b) : null,
            'resultado'                 => (isset($fetchProjetoDetalhes[0]->resultado) && !empty($fetchProjetoDetalhes[0]->resultado)) ? utf8_encode($fetchProjetoDetalhes[0]->resultado) : null,
            'avaliacoes'                => (isset($fetchProjetoMaisDetalhes[0]->avaliacoes) && !empty($fetchProjetoMaisDetalhes[0]->avaliacoes)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->avaliacoes) : null,
            'depoimentos'               => (isset($fetchProjetoMaisDetalhes[0]->depoimentos) && !empty($fetchProjetoMaisDetalhes[0]->depoimentos)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->depoimentos) : null,
            'premios'                   => (isset($fetchProjetoMaisDetalhes[0]->premios) && !empty($fetchProjetoMaisDetalhes[0]->premios)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->premios) : null,
            'principais_dificuldades'   => (isset($fetchProjetoMaisDetalhes[0]->principais_dificuldades) && !empty($fetchProjetoMaisDetalhes[0]->principais_dificuldades)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->principais_dificuldades) : null,
            'dificuldades_superadas'    => (isset($fetchProjetoMaisDetalhes[0]->dificuldades_superadas) && !empty($fetchProjetoMaisDetalhes[0]->dificuldades_superadas)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->dificuldades_superadas) : null,
            'garantir_continuidade'     => (isset($fetchProjetoMaisDetalhes[0]->garantir_continuidade) && !empty($fetchProjetoMaisDetalhes[0]->garantir_continuidade)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->garantir_continuidade) : null,
            'site'                      => (isset($fetchProjetoMaisDetalhes[0]->site) && !empty($fetchProjetoMaisDetalhes[0]->site)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->site) : null,
            'redes_sociais'             => (isset($fetchProjetoMaisDetalhes[0]->redes_sociais) && !empty($fetchProjetoMaisDetalhes[0]->redes_sociais)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->redes_sociais) : null,
            'fotos_videos'              => (isset($fetchProjetoMaisDetalhes[0]->fotos_videos) && !empty($fetchProjetoMaisDetalhes[0]->fotos_videos)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->fotos_videos) : null,
            'adicional'                 => (isset($fetchProjetoMaisDetalhes[0]->adicional) && !empty($fetchProjetoMaisDetalhes[0]->adicional)) ? utf8_encode($fetchProjetoMaisDetalhes[0]->adicional) : null,
        );
        $project->project_details = json_decode(json_encode($projectDetails), false);

        $sqlProjetoResponsavel = "SELECT * FROM `proleitura`.`projeto_responsavel` WHERE `project_id`='". $id ."' LIMIT 1";
        $fetchProjetoResponsavel = parent::free_select($sqlProjetoResponsavel);
        $projectResponsible = array(
            'project_id'                => (isset($fetchProjetoResponsavel[0]->project_id) && !empty($fetchProjetoResponsavel[0]->project_id)) ? $fetchProjetoResponsavel[0]->project_id : null,
            'organizacao'               => (isset($fetchProjetoResponsavel[0]->organizacao) && !empty($fetchProjetoResponsavel[0]->organizacao)) ? utf8_encode($fetchProjetoResponsavel[0]->organizacao) : null,
            'cnpj'                      => (isset($fetchProjetoResponsavel[0]->cnpj) && !empty($fetchProjetoResponsavel[0]->cnpj)) ? $this->mask($fetchProjetoResponsavel[0]->cnpj, '##.###.###/####-##') : null,
            'cidade'                    => (isset($fetchProjetoResponsavel[0]->cidade) && !empty($fetchProjetoResponsavel[0]->cidade)) ? utf8_encode($fetchProjetoResponsavel[0]->cidade) : null,
            'uf'                        => (isset($fetchProjetoResponsavel[0]->uf) && !empty($fetchProjetoResponsavel[0]->uf)) ? utf8_encode($fetchProjetoResponsavel[0]->uf) : null,
            'cep'                       => (isset($fetchProjetoResponsavel[0]->cep) && !empty($fetchProjetoResponsavel[0]->cep)) ? $this->mask($fetchProjetoResponsavel[0]->cep, '#####-###') : null,
            'email'                     => (isset($fetchProjetoResponsavel[0]->email) && !empty($fetchProjetoResponsavel[0]->email)) ? utf8_encode($fetchProjetoResponsavel[0]->email) : null,
            'telefone'                  => (isset($fetchProjetoResponsavel[0]->telefone) && !empty($fetchProjetoResponsavel[0]->telefone)) ? $this->mask($fetchProjetoResponsavel[0]->telefone, (strlen($fetchProjetoResponsavel[0]->telefone) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'celular'                   => (isset($fetchProjetoResponsavel[0]->celular) && !empty($fetchProjetoResponsavel[0]->celular)) ? $this->mask($fetchProjetoResponsavel[0]->celular, (strlen($fetchProjetoResponsavel[0]->celular) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'site'                      => (isset($fetchProjetoResponsavel[0]->site) && !empty($fetchProjetoResponsavel[0]->site)) ? utf8_encode($fetchProjetoResponsavel[0]->site) : null,
            'facebook'                  => (isset($fetchProjetoResponsavel[0]->facebook) && !empty($fetchProjetoResponsavel[0]->facebook)) ? utf8_encode($fetchProjetoResponsavel[0]->facebook) : null,
            'outros_contatos'           => (isset($fetchProjetoResponsavel[0]->outros_contatos) && !empty($fetchProjetoResponsavel[0]->outros_contatos)) ? utf8_encode($fetchProjetoResponsavel[0]->outros_contatos) : null,
            'pessoa_responsavel'        => (isset($fetchProjetoResponsavel[0]->pessoa_responsavel) && !empty($fetchProjetoResponsavel[0]->pessoa_responsavel)) ? utf8_encode($fetchProjetoResponsavel[0]->pessoa_responsavel) : null,
            'pessoa_cargo'              => (isset($fetchProjetoResponsavel[0]->pessoa_cargo) && !empty($fetchProjetoResponsavel[0]->pessoa_cargo)) ? utf8_encode($fetchProjetoResponsavel[0]->pessoa_cargo) : null,
            'pessoa_email'              => (isset($fetchProjetoResponsavel[0]->pessoa_email) && !empty($fetchProjetoResponsavel[0]->pessoa_email)) ? utf8_encode($fetchProjetoResponsavel[0]->pessoa_email) : null,
            'pessoa_telefone'           => (isset($fetchProjetoResponsavel[0]->pessoa_telefone) && !empty($fetchProjetoResponsavel[0]->pessoa_telefone)) ? $this->mask($fetchProjetoResponsavel[0]->pessoa_telefone, (strlen($fetchProjetoResponsavel[0]->pessoa_telefone) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'pessoa_celular'            => (isset($fetchProjetoResponsavel[0]->pessoa_celular) && !empty($fetchProjetoResponsavel[0]->pessoa_celular)) ? $this->mask($fetchProjetoResponsavel[0]->pessoa_celular, (strlen($fetchProjetoResponsavel[0]->pessoa_celular) > 10) ? '(##) #####-####' : '(##) ####-####') : null,
            'pessoa_outros_contatos'    => (isset($fetchProjetoResponsavel[0]->pessoa_outros_contatos) && !empty($fetchProjetoResponsavel[0]->pessoa_outros_contatos)) ? utf8_encode($fetchProjetoResponsavel[0]->pessoa_outros_contatos) : null,
        );
        $project->project_responsible = json_decode(json_encode($projectResponsible), false);

        return $project;
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