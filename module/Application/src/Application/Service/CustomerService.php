<?php
namespace Application\Service;

use Zend\Session\Container;

class CustomerService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addCustomerData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $customerDb = $this->sm->get('CustomerTable');
           $customerId = $customerDb->addCustomer($params);
           if($customerId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Customer added successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getCustomerList($parameters) {
        $customerDb = $this->sm->get('CustomerTable');
        $acl = $this->sm->get('AppAcl');
        return $customerDb->fetchCustomerList($parameters, $acl);
    }
    
    public function getCustomerDetailsById($customerId) {
        $customerDb = $this->sm->get('CustomerTable');
        return $customerDb->fetchCustomerById($customerId);
    }
    
    public function updateCustomerDetails($params){        
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
            $customerDb = $this->sm->get('CustomerTable');
            $customerId = $customerDb->updateCustomer($params);
            if($customerIds){
              $adapter->commit();
              $alertContainer = new Container('alert');
              $alertContainer->alertMsg = 'Customer updated successfully';
            }
            
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
        
    }
    
    public function getCustomerListInfo($parameters) {
        $customerDb = $this->sm->get('CustomerTable');
        return $customerDb->fetchCustomerListInfo($parameters);
    }
    
    public function getUniqueId() {
        $customerDb = $this->sm->get('CustomerTable');
        return $customerDb->fethUniqueId();
    }
    
    public function getTotalCount() {
        $customerDb = $this->sm->get('CustomerTable');
        return $customerDb->fetchTotalCount();
    }
    
    public function sendSMS($param) {
        $customerDb = $this->sm->get('CustomerTable');
        return $customerDb->sendSMSDetails($param);
    }
}
