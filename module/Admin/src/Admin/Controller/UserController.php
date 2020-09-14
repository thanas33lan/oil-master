<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class UserController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $userService = $this->getServiceLocator()->get('UserService');
            $result = $userService->getUserList($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }

    public function addAction()
    {
        $userService = $this->getServiceLocator()->get('UserService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $userService->addUserData($params);
            return $this->_redirect()->toRoute('admin-user');
        } else {
            $roleService = $this->getServiceLocator()->get('RoleService');
            $rolesResult = $roleService->getActiveRoleList();
            return new ViewModel(array(
                'roleData' => $rolesResult,
            ));
        }
    }

    public function editAction()
    {
        $userService = $this->getServiceLocator()->get('UserService');
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $result = $userService->updateUserDetails($param);
            return $this->redirect()->toRoute('admin-user');
        } else {
            $userId = base64_decode($this->params()->fromRoute('id'));
            $userResult = $userService->getUserDetails($userId);
            $roleService = $this->getServiceLocator()->get('RoleService');
            $rolesResult = $roleService->getActiveRoleList();
            return new ViewModel(array(
                'user' => $userResult,
                'roleData' => $rolesResult,
            ));
        }
    }
    
    public function editProfileAction()
    {
        $userService = $this->getServiceLocator()->get('UserService');
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $result = $userService->updateUserDetails($param,'edit-profile');
            return $this->redirect()->toRoute('admin-home');
        } else {
            $userId = base64_decode($this->params()->fromRoute('id'));
            $userResult = $userService->getUserDetails($userId);
            $roleService = $this->getServiceLocator()->get('RoleService');
            $rolesResult = $roleService->getActiveRoleList();
            return new ViewModel(array(
                'user' => $userResult,
                'roleData' => $rolesResult,
            ));
        }
    }
}
