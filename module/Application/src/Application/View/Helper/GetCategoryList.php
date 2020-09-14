<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class GetCategoryList extends AbstractHelper implements ServiceLocatorAwareInterface{
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
        $this->serviceLocator = $serviceLocator;  
        return $this;  
    }
    
    public function getServiceLocator(){  
        return $this->serviceLocator;  
    }
    
    public function __invoke(){
        $sm = $this->getServiceLocator()->getServiceLocator();
        $catDb = $sm->get('CategoryTable');
        $catResult = $catDb->getCategoryList();
        return $catResult;
    }
}
