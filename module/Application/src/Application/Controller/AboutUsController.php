<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AboutUsController extends AbstractActionController{

    public function indexAction(){
        $this->layout()->setVariable('activeTab', 'aboutUsMenu');
        return new ViewModel();
    }
    
    public function boardAction(){
        $this->layout()->setVariable('activeTab', 'aboutUsMenu');
        $member = $this->params()->fromRoute('member');
        if(!isset($member) || $member == "" || $member == null){
            return $this->_redirect()->toUrl('/about-us');
        }
        return new ViewModel(array('member' => $member));
    }


}

