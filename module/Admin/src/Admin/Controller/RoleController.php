<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class RoleController extends AbstractActionController{

    public function indexAction(){
        $request = $this->getRequest();
        if ($request->isPost()){
            $parameters = $request->getPost();
            $roleService = $this->getServiceLocator()->get('RoleService');
            $result = $roleService->getRoleList($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }

    public function addAction(){
        $roleService=$this->getServiceLocator()->get('RoleService');
        if($this->getRequest()->isPost()){
            $params=$this->getRequest()->getPost();
            $result=$roleService->addRoleData($params);
            return $this->_redirect()->toRoute('admin-role');
        }
        else {
            $resourceResult = $roleService->getAllResource();
            return new ViewModel(array(
                'resourceResult' => $resourceResult,
            ));
        }
    }

    public function editAction(){
        $roleService=$this->getServiceLocator()->get('RoleService');
         if($this->getRequest()->isPost()){
            $params=$this->getRequest()->getPost();
            $result=$roleService->updateRoleData($params);
             return $this->redirect()->toRoute('admin-role');
        }
        else{
            $configFile = CONFIG_PATH . DIRECTORY_SEPARATOR . "acl.config.php";
            $config = \Zend\Config\Factory::fromFile($configFile, true);
            $roleId=base64_decode($this->params()->fromRoute('id'));
            $result=$roleService->getRole($roleId);
            $resourceResult = $roleService->getAllResource();
            return new ViewModel(array(
                'result'=>$result,
                'resourceResult' => $resourceResult,
                'resourcePrivilegeMap' => $config
            ));
        }
    }


}

