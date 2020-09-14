<?php
namespace Application\Service;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;

class UserService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addUserData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $userDb = $this->sm->get('UserTable');
           $userId = $userDb->addUser($params);
           if($userId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'User added successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getUserList($parameters) {
        $userDb = $this->sm->get('UserTable');
        $acl = $this->sm->get('AppAcl');
        return $userDb->fetchUserList($parameters, $acl);
    }
    
    public function getUserDetails($userId) {
        $userDb = $this->sm->get('UserTable');
        return $userDb->getUser($userId);
    }
    
    public function updateUserDetails($params,$type=''){        
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
            $userDb = $this->sm->get('UserTable');
            $userId = $userDb->updateUser($params);
            if($userId>0){
              $adapter->commit();
              $alertContainer = new Container('alert');
              if($type == 'edit-profile'){
                $alertContainer->alertMsg = 'Profile updated successfully';
              }else{
                $alertContainer->alertMsg = 'User updated successfully';
              }
            }
            
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
        
    }
    
    public function userLogin($params){
        $userDb = $this->sm->get('UserTable');
        return $userDb->loginProcess($params);
    }
   
    public function getUserListInfo($parameters) {
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchUserListInfo($parameters);
    }
    
     public function saveUserDetails($params){        
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
            $userDb = $this->sm->get('UserTable');
            $userId = $userDb->updateUserInfo($params);
            if($userId>0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'User details saved successfully';
            }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
        
    }

    public function changeUserPassword($params) {
        $alertContainer = new Container('alert');
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
            $userDb = $this->sm->get('UserTable');
            $result = $userDb->modifyUserPassword($params);
            if($result > 0) {
                $adapter->commit();
                $alertContainer->alertMsg = 'Password changed successfully';
            }else{
                $alertContainer->alertMsg = 'Sorry...Something went wrong.Please try again later';
            }
            return $result;
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }

    public function getActiveUsers()
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchActiveUsers();
    }
}
