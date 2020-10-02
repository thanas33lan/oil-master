<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class PurchaseController extends AbstractActionController
{
    public function indexAction()
    {
        $purchaseService = $this->getServiceLocator()->get('PurchaseService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $purchaseService->getPurchaseListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else{
            return new ViewModel(array());
        }
    }

    public function addAction()
    {
        $purchaseService = $this->getServiceLocator()->get('PurchaseService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $purchaseService->savePurchaseData($params);
            return $this->_redirect()->toRoute('admin-purchase');
        } else{
            return new ViewModel(array());
        }
    }

    public function editAction()
    {
        $purchaseService = $this->getServiceLocator()->get('PurchaseService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $purchaseService->savePurchaseData($params);
            return $this->redirect()->toRoute('admin-purchase');
        } else {
            $PurchaseId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'result' => $purchaseService->getPurchaseById($PurchaseId)
            ));
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $purchaseService = $this->getServiceLocator()->get('PurchaseService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $purchaseService->changeStatusById($param)))->setTerminal(true);
        }
    }
}