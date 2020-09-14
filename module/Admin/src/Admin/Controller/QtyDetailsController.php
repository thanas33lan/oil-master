<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class QtyDetailsController extends AbstractActionController
{
    public function indexAction()
    {
        $qtyService = $this->getServiceLocator()->get('QtyService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $qtyService->getQtyListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else{
            return new ViewModel(array());
        }
    }

    public function addAction()
    {
        $qtyService = $this->getServiceLocator()->get('QtyService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $qtyService->saveQtyData($params);
            return $this->_redirect()->toRoute('admin-qty');
        } else{
            return new ViewModel(array());
        }
    }

    public function editAction()
    {
        $qtyService = $this->getServiceLocator()->get('QtyService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $qtyService->saveQtyData($params);
            return $this->redirect()->toRoute('admin-qty');
        } else {
            $qtyId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'result' => $qtyService->getQtyById($qtyId)
            ));
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $qtyService = $this->getServiceLocator()->get('QtyService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $qtyService->changeStatusById($param)))->setTerminal(true);
        }
    }
}