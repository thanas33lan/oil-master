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

class CategoryService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function saveCategoryData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $categoryDb = $this->sm->get('CategoryTable');
           $CategoryId = $categoryDb->saveCategoryDetails($params);
           if($CategoryId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Category detail saved successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getCategoryListInGrid($parameters) {
        $categoryDb = $this->sm->get('CategoryTable');
        $acl = $this->sm->get('AppAcl');
        return $categoryDb->fetchCategoryListInGrid($parameters, $acl);
    }
    
    public function getAllActiveCategory() {
        $categoryDb = $this->sm->get('CategoryTable');
        return $categoryDb->fetchAllActiveCategory();
    }
    
    public function getCategoryById($id) {
        $categoryDb = $this->sm->get('CategoryTable');
        return $categoryDb->fetchCategoryById($id);
    }
    
    public function deleteById($id) {
        $categoryDb = $this->sm->get('CategoryTable');
        return $categoryDb->deleteByCategoryId($id);
    }
    
    public function changeStatusById($id) {
        $categoryDb = $this->sm->get('CategoryTable');
        return $categoryDb->changeStatusByCategoryId($id);
    }
}
?>