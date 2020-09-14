<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class CategoryController extends AbstractActionController
{
    public function indexAction()
    {
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $categoryService->getCategoryListInGrid($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }else {
            return new ViewModel(array(
                'categorylist' => $categoryService->getAllActiveCategory()
            ));
        }
    }

    public function addAction()
    {
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $categoryService->saveCategoryData($params);
            return $this->_redirect()->toRoute('admin-category');
        } else {
            return new ViewModel(array(
                'categorylist' => $categoryService->getAllActiveCategory()
            ));
        }
    }

    public function editAction()
    {
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $categoryService->saveCategoryData($param);
            return $this->redirect()->toRoute('admin-category');
        } else {
            $categoryId = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'categorylist' => $categoryService->getAllActiveCategory(),
                'result' => $categoryService->getCategoryById($categoryId)
            ));
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $categoryService = $this->getServiceLocator()->get('CategoryService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $categoryService->deleteById($param['categoryId'])))->setTerminal(true);
        }
    }
    
    public function changeStatusAction()
    {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getPost();
            $categoryService = $this->getServiceLocator()->get('CategoryService');
            $viewModel = new ViewModel();
            return $viewModel->setVariables(array('result' => $categoryService->changeStatusById($param)))->setTerminal(true);
        }
    }
}