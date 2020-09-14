<?php
namespace Application\Controller;

use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController{

    public function indexAction(){
       $packageId = '';
       $pkAgtMapId = '';
       $pkBookId = '';
       $request = $this->getRequest();
        if ($request->isPost()){
            $params = $request->getPost();
            $customerService = $this->getServiceLocator()->get('CustomerService');
            $url = $customerService->customerLogin($params);
            return $this->_redirect()->toUrl($url);
        }else{
              $packageId = base64_decode($this->params()->fromQuery('fromPkview'));
              $pkAgtMapId = base64_decode($this->params()->fromQuery('fromPkreview'));
              $pkBookId = base64_decode($this->params()->fromQuery('fromPkbk'));
              return new ViewModel(array(
                    'pkId'=>$packageId,
                    'pkAgtMapId'=>$pkAgtMapId,
                    'pkBookId'=>$pkBookId
                ));
        }
    }
    
    public function logoutAction(){
        $sessionLogin = new Container('customer');
        $sessionLogin->getManager()->getStorage()->clear('customer');
        return $this->redirect()->toUrl('/');
    }


}

