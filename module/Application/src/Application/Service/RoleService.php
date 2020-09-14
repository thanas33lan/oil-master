<?php
namespace Application\Service;

use Zend\Session\Container;
use Zend\Config\Writer\PhpArray;

class RoleService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addRoleData($params){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $roleDb = $this->sm->get('RoleTable');
           $result = $roleDb->addRole($params);
           if($result>0){
            $roleDb->mapRolePrivilege($params);
            $adapter->commit();
               $alertContainer = new Container('alert');
               $alertContainer->alertMsg = 'Role added successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getRoleList($parameters){
        $roleDb = $this->sm->get('RoleTable');
        $acl = $this->sm->get('AppAcl');
        return $roleDb->fetchRoleList($parameters, $acl);
           
    }
    
    public function getRole($roleId){
        $roleDb = $this->sm->get('RoleTable');
        return $roleDb->fetchRole($roleId);
    }
    
    public function updateRoleData($params){        
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
            $roleDb = $this->sm->get('RoleTable');
            $roleDb->updateRole($params);
            $roleDb->mapRolePrivilege($params);
            $adapter->commit();
            $alertContainer = new Container('alert');
            $alertContainer->alertMsg = 'Role updated successfully';
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
        
    }
    
    public function getActiveRoleList(){
        $roleDb = $this->sm->get('RoleTable');
        return $roleDb->fetchActiveRoleList();
    }
    
    public function getAllResource(){
        $roleDb = $this->sm->get('RoleTable');
        return $roleDb->fetchAllResource();
    }
}