<?php

namespace Application;

use Zend\Session\Container;
use Application\Model\Acl;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // No need to call presetter if request is from CLI
        if (php_sapi_name() != 'cli') {
            $eventManager->attach('dispatch', array($this, 'preSetter'), 100);
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'dispatchError'), -999);
        }
    }

    public function dispatchError(MvcEvent $event)
    {
        $error = $event->getError();

        if (empty($error) || $error != "ACL_ACCESS_DENIED") {
            return;
        }

        $baseModel = new ViewModel();
        $baseModel->setTemplate('layout/admin');

        // passing the ACL object 
        $sm = $event->getApplication()->getServiceManager();
        $acl = $sm->get('AppAcl');
        $baseModel->acl = $acl;


        $model = new ViewModel();
        $model->setTemplate('error/403');

        $baseModel->addChild($model, 'aclError');
        $baseModel->setTerminal(true);

        $event->setViewModel($baseModel);

        $response = $event->getResponse();
        $response->setStatusCode(403);

        $event->setResponse($response);
        $event->setResult($baseModel);

        return false;
    }

    public function preSetter(MvcEvent $e)
    {
        $tempName = explode('Controller', $e->getRouteMatch()->getParam('controller'));
        if (($e->getRouteMatch()->getParam('controller') != 'Admin\Controller\Login')) {
            //  \Zend\Debug\Debug::dump($e->getRouteMatch()->getParam('controller'));die;
            if (substr($tempName[0], 0, -1) == 'Admin') {
                $session = new Container('user');
                if (!isset($session->userId) || $session->userId == "") {
                    if( ! $e->getRequest()->isXmlHttpRequest()) {
                        $url = $e->getRouter()->assemble(array(), array('name' => 'admin-login'));
                        $response = $e->getResponse();
                        $response->getHeaders()->addHeaderLine('Location', $url);
                        $response->setStatusCode(302);
                        $response->sendHeaders();

                        // To avoid additional processing
                        // we can attach a listener for Event Route with a high priority
                        $stopCallBack = function($event) use ($response) {
                            $event->stopPropagation();
                            return $response;
                        };
                        //Attach the "break" as a listener with a high priority
                        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);
                        return $response;
                    }
                } 
                if ($e->getRequest()->isXmlHttpRequest()) {
                    return;
                }else{
                    $sm = $e->getApplication()->getServiceManager();
                    $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
                    $acl = $sm->get('AppAcl');
                    $viewModel->acl = $acl;

                    $params = $e->getRouteMatch()->getParams();
                    $resource = $params['controller'];
                    $privilege = $params['action'];
                    $role = $session->roleCode;
                    if ((!$acl->hasResource($resource) || (!$acl->isAllowed($role, $resource, $privilege))) ) {
                        $e->setError('ACL_ACCESS_DENIED')->setParam('route', $e->getRouteMatch());
                        $e->getApplication()->getEventManager()->trigger('dispatch.error', $e);
                    }
                    
                }
            }
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'GetCategoryList' => 'Application\View\Helper\GetCategoryList',
                'GetCategoryFileList' => 'Application\View\Helper\GetCategoryFileList',
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AppAcl' => function ($sm) {
                    $resourceDb = $sm->get('ResourceTable');
                    $roleDb = $sm->get('RoleTable');
                    return new Acl($resourceDb->fetchAllResourceMap(), $roleDb->fetchAllRole());
                }
            )
        );
    }
}
