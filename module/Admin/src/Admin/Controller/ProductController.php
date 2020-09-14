<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class ProductController extends AbstractActionController
{
    public function indexAction()
    {
        $productService = $this->getServiceLocator()->get('ProductService');
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $productService->getProductListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        } else{
            return new ViewModel(array(
                'categorylist' => $categoryService->getAllActiveCategory()
            ));
        }
    }

    public function addAction()
    {
        $productService = $this->getServiceLocator()->get('ProductService');
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $productService->saveProductData($params);
            return $this->_redirect()->toRoute('admin-product');
        } else{
            return new ViewModel(array(
                'categorylist' => $categoryService->getAllActiveCategory()
            ));
        }
    }

    public function editAction()
    {
        $productService = $this->getServiceLocator()->get('ProductService');
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $productService->saveProductData($params);
            return $this->redirect()->toRoute('admin-product');
        } else {
            $productId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'categorylist' => $categoryService->getAllActiveCategory(),
                'result' => $productService->getProductById($productId)
            ));
        }
    }

    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $productService = $this->getServiceLocator()->get('ProductService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $productService->changeStatusById($param)))->setTerminal(true);
        }
    }
}