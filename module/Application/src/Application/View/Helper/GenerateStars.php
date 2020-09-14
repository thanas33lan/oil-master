<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class GenerateStars extends AbstractHelper implements ServiceLocatorAwareInterface{
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
        $this->serviceLocator = $serviceLocator;  
        return $this;  
    }
    
    public function getServiceLocator(){  
        return $this->serviceLocator;  
    }
    // $inputFormat = yyyy-mm-dd
    public function __invoke($numberOfStars){
        $grey = 5 - $numberOfStars;
        //echo "(";
        echo str_repeat("<i class='icon icon-star' style='color:#d60d00'></i>",$numberOfStars);
        echo str_repeat("<i class='icon icon-star' style='color:#ccc'></i>",$grey);
        //echo ")";
        
    }
}