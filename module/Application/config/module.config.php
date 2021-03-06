<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'home-site' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            
           
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'common' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/common[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Common',
                        'action' => 'index',
                    ),
                ),
            ),
            'about-us' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/about-us[/]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\AboutUs',
                        'action' => 'index',
                    ),
                ),
            ),
            'login' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Login',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'logout' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Login',
                        'action' => 'logout',
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/contact',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'contact',
                    ),
                ),
            ),
            'captcha' => array(
                'type' => 'segment',
                'options' => array(
                     'route' => '/captcha[/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Captcha',
                        'action' => 'index',
                    ),
                ),
            ),
            'checkcaptcha' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/checkcaptcha',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Captcha',
                        'action' => 'check-captcha',
                    ),
                ),
            ),
            'my-account' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/my-account[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\MyAccount',
                        'action' => 'index',
                    ),
                ),
            ), 
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Common' => 'Application\Controller\CommonController',
            'Application\Controller\Login' => 'Application\Controller\LoginController',
            'Application\Controller\AboutUs' => 'Application\Controller\AboutUsController',
            'Application\Controller\Captcha' => 'Application\Controller\CaptchaController',
            'Application\Controller\MyAccount' => 'Application\Controller\MyAccountController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
             'category_helper' => 'Application\View\Helper\CategoryHelper',
        )
   ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'send-mail' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'send-mail',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                            'action' => 'send-mail',
                        ),
                    ),
                ),
                'import-cohort-data' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'import-cohort-data',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                            'action' => 'import-cohort-data',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
