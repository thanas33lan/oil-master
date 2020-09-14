<?php
namespace Application\Service;

use Zend\Session\Container;
use Exception;
use Zend\Db\Sql\Sql;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ZoneService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function saveZoneData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $zoneDb = $this->sm->get('ZoneTable');
           $zoneId = $zoneDb->saveZoneDetails($params);
           if($zoneId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Zone detail saved successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getZoneListInGrid($parameters) {
        $zoneDb = $this->sm->get('ZoneTable');
        $acl = $this->sm->get('AppAcl');
        return $zoneDb->fetchZoneListInGrid($parameters, $acl);
    }
    
    public function getAllActiveZone() {
        $zoneDb = $this->sm->get('ZoneTable');
        return $zoneDb->fetchAllActiveZone();
    }
    
    public function getZoneById($id) {
        $zoneDb = $this->sm->get('ZoneTable');
        return $zoneDb->fetchZoneById($id);
    }
    
    public function deleteById($id) {
        $zoneDb = $this->sm->get('ZoneTable');
        return $zoneDb->deleteByZoneId($id);
    }
    
    public function changeStatusById($id) {
        $zoneDb = $this->sm->get('ZoneTable');
        return $zoneDb->changeStatusByZoneId($id);
    }
}
?>