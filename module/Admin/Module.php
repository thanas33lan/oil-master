<?php

namespace Admin;

use Application\Model\RoleTable;
use Application\Model\ResourceTable;
use Application\Model\UserRolesTable;
use Application\Model\UserTable;
use Application\Model\CustomerTable;
use Application\Model\StateTable;
use Application\Model\CategoryTable;
use Application\Model\ZoneTable;
use Application\Model\UserZoneTable;
use Application\Model\CustomerZoneTable;
use Application\Model\ProductTable;
use Application\Model\ProductQtyMapTable;
use Application\Model\QtyTable;
use Application\Model\UserProductMapTable;
use Application\Model\QtyDetailsTable;
use Application\Model\SupplierTable;
use Application\Model\PurchaseTable;

use Application\Service\CommonService;
use Application\Service\RoleService;
use Application\Service\UserService;
use Application\Service\CustomerService;
use Application\Service\CategoryService;
use Application\Service\ZoneService;
use Application\Service\ProductService;
use Application\Service\QtyService;
use Application\Service\SupplierService;
use Application\Service\PurchaseService;

class Module {

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'RoleTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new RoleTable($dbAdapter);
                    return $table;
                },
                'ResourceTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ResourceTable($dbAdapter);
                    return $table;
                }, 
                'UserRolesTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserRolesTable($dbAdapter);
                    return $table;
                },
                
                'UserTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                },
                'CustomerTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CustomerTable($dbAdapter);
                    return $table;
                },
                'StateTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new StateTable($dbAdapter);
                    return $table;
                },
                'CategoryTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CategoryTable($dbAdapter);
                    return $table;
                },
                'ZoneTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ZoneTable($dbAdapter);
                    return $table;
                },
                'UserZoneTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserZoneTable($dbAdapter);
                    return $table;
                },
                'CustomerZoneTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CustomerZoneTable($dbAdapter);
                    return $table;
                },
                'ProductTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ProductTable($dbAdapter);
                    return $table;
                },
                'QtyTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new QtyTable($dbAdapter);
                    return $table;
                },
                'ProductQtyMapTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ProductQtyMapTable($dbAdapter);
                    return $table;
                },
                'UserProductMapTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserProductMapTable($dbAdapter);
                    return $table;
                },
                'QtyDetailsTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new QtyDetailsTable($dbAdapter);
                    return $table;
                },
                'SupplierTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SupplierTable($dbAdapter);
                    return $table;
                },
                'PurchaseTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new PurchaseTable($dbAdapter);
                    return $table;
                },
                
                'CommonService' => function($sm) {
                    return new CommonService($sm);
                },
                'RoleService' => function($sm) {
                    return new RoleService($sm);
                },
                'UserService' => function($sm) {
                    return new UserService($sm);
                },
                'CustomerService' => function($sm) {
                    return new CustomerService($sm);
                },
                'CategoryService' => function($sm) {
                    return new CategoryService($sm);
                },
                'ZoneService' => function($sm) {
                    return new ZoneService($sm);
                },
                'ProductService' => function($sm) {
                    return new ProductService($sm);
                },
                'QtyService' => function($sm) {
                    return new QtyService($sm);
                },
                'SupplierService' => function($sm) {
                    return new SupplierService($sm);
                },
                'PurchaseService' => function($sm) {
                    return new PurchaseService($sm);
                },
            ),
        );
    }
}