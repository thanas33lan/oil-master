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

class SupplierService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function saveSupplierData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $supplierDb = $this->sm->get('SupplierTable');
           $supplierId = $supplierDb->saveSupplierDetails($params);
           if($supplierId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Supplier detail saved successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getSupplierListInGrid($parameters) {
        $supplierDb = $this->sm->get('SupplierTable');
        $acl = $this->sm->get('AppAcl');
        return $supplierDb->fetchSupplierListInGrid($parameters, $acl);
    }
    
    public function getAllActiveSupplier() {
        $supplierDb = $this->sm->get('SupplierTable');
        return $supplierDb->fetchAllActiveSupplier();
    }
    
    public function getLastInsertedId() {
        $supplierDb = $this->sm->get('SupplierTable');
        return $supplierDb->fetchLastInsertedId();
    }
    
    public function getSupplierById($id) {
        $supplierDb = $this->sm->get('SupplierTable');
        return $supplierDb->fetchSupplierById($id);
    }
    
    public function deleteById($id) {
        $supplierDb = $this->sm->get('SupplierTable');
        return $supplierDb->deleteBySupplierId($id);
    }
    
    public function changeStatusById($id) {
        $supplierDb = $this->sm->get('SupplierTable');
        return $supplierDb->changeStatusBySupplierId($id);
    }
}
?>