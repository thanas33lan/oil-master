<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class SupplierController extends AbstractActionController
{
    public function indexAction()
    {
        $supplierService = $this->getServiceLocator()->get('SupplierService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $supplierService->getSupplierListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else{
            return new ViewModel(array(
                'lastId' => $supplierService->getLastInsertedId()
            ));
        }
    }

    public function addAction()
    {
        $supplierService = $this->getServiceLocator()->get('SupplierService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $supplierService->saveSupplierData($params);
            return $this->_redirect()->toRoute('admin-supplier');
        }
    }

    public function editAction()
    {
        $supplierService = $this->getServiceLocator()->get('SupplierService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $supplierService->saveSupplierData($params);
            return $this->redirect()->toRoute('admin-supplier');
        } else {
            $supplierId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'lastId' => $supplierService->getLastInsertedId(),
                'result' => $supplierService->getSupplierById($supplierId)
            ));
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $supplierService = $this->getServiceLocator()->get('SupplierService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $supplierService->changeStatusById($param)))->setTerminal(true);
        }
    }
}