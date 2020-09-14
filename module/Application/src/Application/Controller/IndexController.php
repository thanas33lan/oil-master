<?php
namespace Application\Controller;

use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController{
    
    public function indexAction(){
        $this->layout()->setVariable('activeTab', 'homeMenu');
        return new ViewModel();        
    }
    
    public function contactAction(){
        $contactService = $this->getServiceLocator()->get('CommonService');
        if($this->getRequest()->isPost()){
            $params=$this->getRequest()->getPost();
            $contactService->addContact($params);
            return $this->_redirect()->toRoute('home');
        }
    }
    
    public function registerEventAction(){
        $customerService = $this->getServiceLocator()->get('CustomerService');
        if($this->getRequest()->isPost()){
            $params=$this->getRequest()->getPost();
            $customerService->addCustomer($params);
            return $this->_redirect()->toRoute('home');
        }
    }
}

