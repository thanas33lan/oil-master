<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class MyAccountController extends AbstractActionController{
    
    public function indexAction(){
        $this->layout()->setVariable('activeTab', 'myAccountMenu');
        $request = $this->getRequest();
        if ($request->isPost()){
            $params = $request->getPost();
            $questionService = $this->getServiceLocator()->get('BiosafetyUserService');
            $result = $questionService->updateBiosafetyUserDetails($params);
            return $this->redirect()->toUrl('/my-account');

        }else{
            $bsUserLogincontainer = new Container('bsUserLoginContainer');
            if(!isset($bsUserLogincontainer->bsUserId) || trim($bsUserLogincontainer->bsUserId) == ''){
                return $this->redirect()->toUrl('/'); 
            }
            $bioSafetyService = $this->getServiceLocator()->get('BiosafetyUserService');
            $userService = $this->getServiceLocator()->get('UserService');
            $bioSafetyResult = $bioSafetyService->getLoginUSerById();
            $countryResult = $userService->getAllCountries();
            $rbpJobDetails = $userService->getAllJobDetail();
            return array(
                'bioSafetyResult'=>$bioSafetyResult,
                'rbpJobDetails'=>$rbpJobDetails,
                'countryResult'=>$countryResult
            );
        }
    }

    public function certificateAction(){
        $this->layout()->setVariable('activeTab', 'myAccountMenu');
        $bsUserLogincontainer = new Container('bsUserLoginContainer');
        if(!isset($bsUserLogincontainer->bsUserId) || trim($bsUserLogincontainer->bsUserId) == ''){
            return $this->redirect()->toUrl('/biosafety/course'); 
        }
        $testService = $this->getServiceLocator()->get('TestService');
        $testConfigService = $this->getServiceLocator()->get('TestConfigService');
        $preResult = $testService->getPreResultDetails();
        $postResult = $testService->getPostResultDetails();
        $testResult = $testService->getTestsDetailsbyId();
        $configResult = $testConfigService->getTestConfig();
        return array(
            'testResult'=>$testResult,
            'preResult'=>$preResult,
            'postResult'=>$postResult,
            'configResult'=>$configResult
        );
    }

}

