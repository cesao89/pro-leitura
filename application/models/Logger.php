<?php
/**
 * Created by PhpStorm.
 * User: cesar.domingos
 * Date: 04/05/2016
 * Time: 11:50
 */

class Application_Model_Logger
{
    // Model Log4PHP
    private $log;

    // Tags
    private $tags = array();

    // Tags Default
    private $tagsDefault = array(
        'account_type'          => NULL,
        'action'                => NULL,
        'aggregated_channel'    => NULL,
        'application_name'      => NULL,
        'backend_plan'          => NULL,
        'billing_parcel'        => NULL,
        'billing_type'          => NULL,
        'business_segment'      => NULL,
        'campaign'              => NULL,
        'channel'               => NULL,
        'country'               => NULL,
        'days_gratuity'         => NULL,
        'ddd'                   => NULL,
        'ddi'                   => NULL,
        'earlier_backend_plan'  => NULL,
        'earlier_plan'          => NULL,
        'earlier_price'         => NULL,
        'email_provider'        => NULL,
        'error_code'            => NULL,
        'error_reason'          => NULL,
        'event_date'            => NULL,
        'event_id'              => NULL,
        'event_status'          => NULL,
        'family_product'        => NULL,
        'incentive_type'        => NULL,
        'ip'                    => NULL,
        'kw'                    => NULL,
        'la'                    => NULL,
        'line_type'             => NULL,
        'log_type'              => NULL,
        'msg'                   => NULL,
        'operational_system'    => NULL,
        'partner'               => NULL,
        'partner_reference'     => NULL,
        'person_type'           => NULL,
        'plan'                  => NULL,
        'price'                 => NULL,
        'promotion'             => NULL,
        'reason'                => NULL,
        'response'              => NULL,
        'segment_name'          => NULL,
        'square'                => NULL,
        'status_code'           => NULL,
        'store'                 => NULL,
        'subscription_type'     => NULL,
        'suplier'               => NULL,
        'suplier_reference'     => NULL,
        'timezone'              => NULL,
        'user'                  => NULL,
        'user_type'             => NULL
    );

    // Tags Mandatory
    private $tagsMandatory = array('partner', 'application_name', 'action', 'event_date', 'event_status', 'user', 'user_type');

    /**
     * Application_Model_Logger constructor.
     * @param string $loggerName
     */
    public function __construct($loggerName = 'default')
    {
        Logger::configure(APPLICATION_PATH . '/configs/config.xml');
        $this->log = Logger::getLogger($loggerName);
        $this->tags['partner'] = 'VIVO';
        $this->tags['application_name'] = 'Portal de Vendas VIVO Seguros';
    }

    /**
     * Metodo para definicao de TAG's
     * @param $tags
     * @return bool
     */
    public function defineTags($tags)
    {
        if(isset($tags) && !empty($tags) && is_array($tags)){
            foreach ($tags as $key => $value){
                $this->tags[$key] = $value;
            }
            return true;
        }
        return false;
    }

    /**
     * Metodo para registrar LOG
     * @param $type
     * @param null $tags
     * @return bool
     */
    public function log($type, $tags=NULL)
    {
        if(isset($tags) && !empty($tags)){
            if(!$this->defineTags($tags)){
                return false;
            }
        }

        $this->tags['event_date'] = $this->formatDateTime();

        $integrity = $this->informationIntegrity();
        if($integrity){
            $logMe = json_encode($this->tags);
            $this->log->$type($logMe);
            $this->tags = $this->tagsDefault;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Metodo para validacao da integridade da informacao:
     * + Remove campos invalidos
     * + Remove campos que estao NULL (default)
     * + Valida os campos obrigatorios
     * @return bool
     */
    private function informationIntegrity()
    {
        // Valid Integrity of Keys
        $diff = array_diff_key($this->tags, $this->tagsDefault);
        if(count($diff) > 0){
            foreach ($diff as $key => $value){
                unset($this->tags[$key]);
            }
        }

        // Remove Empty Keys
        foreach($this->tags as $key => $value){
            if($value === NULL){
                unset($this->tags[$key]);
            }
        }

        // Verify Mandatory Keys
        foreach ($this->tagsMandatory as $require){
            if(!array_key_exists($require, $this->tags)){
                return false;
            }
        }

        return true;
    }

    /**
     * Metodo para retornar Data Hora Completa com Milisegundos
     * @return string
     */
    private function formatDateTime()
    {
        $microSeconds = microtime(true);
        $microTime = sprintf("%06d",($microSeconds - floor($microSeconds)) * 1000000);
        $dateTimeModel = new DateTime( date('Y-m-d H:i:s.'.$microTime, $microSeconds) );
        return substr($dateTimeModel->format("Y-m-d H:i:s,u"), 0, (strlen($dateTimeModel->format("Y-m-d H:i:s,u"))-3));
    }
}