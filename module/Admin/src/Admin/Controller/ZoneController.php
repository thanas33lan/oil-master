<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class ZoneController extends AbstractActionController
{
    public function indexAction()
    {
        $zoneService = $this->getServiceLocator()->get('ZoneService');
        $userService = $this->getServiceLocator()->get('UserService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $zoneService->getZoneListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else{
            return new ViewModel(array(
                'zonelist' => $zoneService->getAllActiveZone(),
                'userlist' => $userService->getActiveUsers()
            ));
        }
    }

    public function addAction()
    {
        $zoneService = $this->getServiceLocator()->get('ZoneService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $zoneService->saveZoneData($params);
            return $this->_redirect()->toRoute('admin-zone');
        }
    }

    public function editAction()
    {
        $zoneService = $this->getServiceLocator()->get('ZoneService');
        $userService = $this->getServiceLocator()->get('UserService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $zoneService->saveZoneData($params);
            return $this->redirect()->toRoute('admin-zone');
        } else {
            $ZoneId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'zonelist' => $zoneService->getAllActiveZone(),
                'userlist' => $userService->getActiveUsers(),
                'result' => $zoneService->getZoneById($ZoneId)
            ));
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $zoneService = $this->getServiceLocator()->get('ZoneService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $zoneService->changeStatusById($param)))->setTerminal(true);
        }
    }
}