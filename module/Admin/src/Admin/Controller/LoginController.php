<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class LoginController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userService = $this->getServiceLocator()->get('UserService');
            $route = $userService->userLogin($params);
            return $this->redirect()->toRoute($route);
        }
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function logoutAction()
    {
        $logincontainer = new Container('user');
        $logincontainer->getManager()->getStorage()->clear('user');
        return $this->redirect()->toRoute("admin-login");
    }

    public function editAction()
    {
        $logincontainer = new Container('user');
        $userService = $this->getServiceLocator()->get('userService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $userService->updatuserData($params);
            return $this->redirect()->toRoute('admin-home');
        } else {
            $userId = $logincontainer->userId;
            $result = $userService->getuser($userId);
            $roleService = $this->getServiceLocator()->get('RoleService');
            $roleResult = $roleService->getActiveRoleList();
            return new ViewModel(array(
                'userData' => $result,
                'roleData' => $roleResult
            ));
        }
    }
}
