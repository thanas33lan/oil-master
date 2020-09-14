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

class QtyService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function saveQtyData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $qtyDb = $this->sm->get('QtyDetailsTable');
           $qtyId = $qtyDb->saveQtyDetails($params);
           if($qtyId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Qty detail saved successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getQtyListInGrid($parameters) {
        $qtyDb = $this->sm->get('QtyDetailsTable');
        $acl = $this->sm->get('AppAcl');
        return $qtyDb->fetchQtyListInGrid($parameters, $acl);
    }
    
    public function getAllActiveQty() {
        $qtyDb = $this->sm->get('QtyDetailsTable');
        return $qtyDb->fetchAllActiveQty();
    }
    
    public function getQtyById($id) {
        $qtyDb = $this->sm->get('QtyDetailsTable');
        return $qtyDb->fetchQtyById($id);
    }
    
    public function deleteById($id) {
        $qtyDb = $this->sm->get('QtyDetailsTable');
        return $qtyDb->deleteByQtyId($id);
    }
    
    public function changeStatusById($id) {
        $qtyDb = $this->sm->get('QtyDetailsTable');
        return $qtyDb->changeStatusByQtyId($id);
    }
}
?>