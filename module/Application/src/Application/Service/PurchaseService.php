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

class PurchaseService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function savePurchaseData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $purchaseDb = $this->sm->get('PurchaseDetailsTable');
           $purchaseId = $purchaseDb->savePurchaseDetails($params);
           if($purchaseId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Purchase detail saved successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getPurchaseListInGrid($parameters) {
        $purchaseDb = $this->sm->get('PurchaseDetailsTable');
        $acl = $this->sm->get('AppAcl');
        return $purchaseDb->fetchPurchaseListInGrid($parameters, $acl);
    }
    
    public function getAllActivePurchase() {
        $purchaseDb = $this->sm->get('PurchaseDetailsTable');
        return $purchaseDb->fetchAllActivePurchase();
    }
    
    public function getPurchaseById($id) {
        $purchaseDb = $this->sm->get('PurchaseDetailsTable');
        return $purchaseDb->fetchPurchaseById($id);
    }
    
    public function deleteById($id) {
        $purchaseDb = $this->sm->get('PurchaseDetailsTable');
        return $purchaseDb->deleteByPurchaseId($id);
    }
    
    public function changeStatusById($id) {
        $purchaseDb = $this->sm->get('PurchaseDetailsTable');
        return $purchaseDb->changeStatusByPurchaseId($id);
    }
}
?>