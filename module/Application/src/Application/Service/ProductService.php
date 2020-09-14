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

class ProductService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function saveProductData($params){
       
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
       try {
           $productDb = $this->sm->get('ProductTable');
           $productId = $productDb->saveProductDetails($params);
           if($productId > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Product detail saved successfully';
           }
       }
       catch (Exception $exc) {
           $adapter->rollBack();
           error_log($exc->getMessage());
           error_log($exc->getTraceAsString());
       }
    }
    
    public function getProductListInGrid($parameters) {
        $productDb = $this->sm->get('ProductTable');
        $acl = $this->sm->get('AppAcl');
        return $productDb->fetchProductListInGrid($parameters, $acl);
    }
    
    public function getAllActiveProduct() {
        $productDb = $this->sm->get('ProductTable');
        return $productDb->fetchAllActiveProduct();
    }
    
    public function getProductById($id) {
        $productDb = $this->sm->get('ProductTable');
        return $productDb->fetchProductById($id);
    }
    
    public function deleteById($id) {
        $productDb = $this->sm->get('ProductTable');
        return $productDb->deleteByProductId($id);
    }
    
    public function changeStatusById($id) {
        $productDb = $this->sm->get('ProductTable');
        return $productDb->changeStatusByProductId($id);
    }
}
?>