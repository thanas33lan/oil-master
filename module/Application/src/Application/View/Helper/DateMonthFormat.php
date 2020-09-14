<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class DateMonthFormat extends AbstractHelper implements ServiceLocatorAwareInterface{
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
        $this->serviceLocator = $serviceLocator;  
        return $this;  
    }
    
    public function getServiceLocator(){  
        return $this->serviceLocator;  
    }
    // $inputFormat = yyyy-mm-dd
    public function __invoke($inputFormat){
        if($inputFormat == "" || $inputFormat == null) return "";
        $inputArray = explode("-",$inputFormat);
        return $inputArray[1]."/".$inputArray[0];
    }
}