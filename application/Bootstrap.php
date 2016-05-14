<?php

/**
 * Default base class for bootstrapping requested modules
 *
 * @author     Fedor Petryk
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initAdditionalConfigs()
    {
        $conf = new Zend_Config_Ini(APPLICATION_PATH . '/config/system_types.ini');
        Zend_Registry::set('types', $conf->toArray());

        $confLang = new Zend_Config_Ini(APPLICATION_PATH . '/config/languages.ini');
        Zend_Registry::set('languages', $confLang->toArray());
    }

    public function _initRouter()
    {
        /** * @TODO Add caching for routes in memcache */

        $front = $this->getResource('FrontController');
        /** @var Zend_Controller_Router_Rewrite $router */
        $router = $front->getRouter();

        $conf        = Zend_Registry::get('languages');
        $languages   = implode('|', array_keys($conf['languages']));
        $defaultLang = Zend_Registry::get('appConfig')['lang'];

        $langRoute = new Zend_Controller_Router_Route(
            '/:lang',
            ['lang' => $defaultLang],
            ['lang' => '(' . $languages . ')+']
        );

        $router->addRoute(
            'default',
            new Zend_Controller_Router_Route(
                ':module/:controller/:action/*',
                [
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index'
                ]
            )
        );

        $router->addRoute(
            'home',
            new Zend_Controller_Router_Route(
                '/',
                [
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index'
                ]
            )
        );

        $router->addRoute(
            'showAll',
            new Zend_Controller_Router_Route(
                'showAll/',
                [
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index',
                    'param'      => 'showAll'
                ],
                [
                    'param' => '\d+'
                ]
            )
        );

        $router->addRoute(
            'login',
            new Zend_Controller_Router_Route(
                'login',
                [
                    'controller' => 'auth',
                    'action'     => 'login',
                    'module'     => 'admin'
                ]
            )
        );

        $router->addRoute(
            'page',
            new Zend_Controller_Router_Route(
                'page/:slug/',
                [
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'city',
            new Zend_Controller_Router_Route(
                'tag/:tag/',
                [
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'cities',
            new Zend_Controller_Router_Route(
                'cities/',
                [
                    'controller' => 'page',
                    'action'     => 'cities',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'sitemap',
            new Zend_Controller_Router_Route(
                'sitemap/',
                [
                    'controller' => 'page',
                    'action'     => 'sitemap',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'jobs',
            new Zend_Controller_Router_Route(
                'jobs/',
                [
                    'controller' => 'page',
                    'action'     => 'jobs',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'projects',
            new Zend_Controller_Router_Route(
                'projects/',
                [
                    'controller' => 'page',
                    'action'     => 'projects',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'contacts',
            new Zend_Controller_Router_Route(
                'contacts/',
                [
                    'controller' => 'index',
                    'action'     => 'contacts',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'news',
            new Zend_Controller_Router_Route(
                'news/',
                [
                    'controller' => 'page',
                    'action'     => 'news',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'blog',
            new Zend_Controller_Router_Route(
                'blog/',
                [
                    'controller' => 'page',
                    'action'     => 'blog',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'projects-inside',
            new Zend_Controller_Router_Route(
                'projects/:galleryId',
                [
                    'controller' => 'page',
                    'action'     => 'project',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'no-page',
            new Zend_Controller_Router_Route(
                'no-page',
                [
                    'controller' => 'error',
                    'action'     => 'no-page',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'sign-in',
            new Zend_Controller_Router_Route(
                'sign-in',
                [
                    'controller' => 'index',
                    'action'     => 'sign-in',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'profile',
            new Zend_Controller_Router_Route(
                'profile',
                [
                    'controller' => 'index',
                    'action'     => 'profile',
                    'module'     => 'profile'
                ]
            )
        );

        $router->addRoute(
            'forgot-password',
            new Zend_Controller_Router_Route(
                'forgot-password',
                [
                    'controller' => 'index',
                    'action'     => 'forgot-password',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'cvs-jobs',
            new Zend_Controller_Router_Route(
                'cvs-jobs',
                [
                    'controller' => 'management',
                    'action'     => 'cvs-jobs',
                    'module'     => 'profile'
                ]
            )
        );

        $router->addRoute(
            'conversations',
            new Zend_Controller_Router_Route(
                'conversations',
                [
                    'controller' => 'index',
                    'action'     => 'conversations',
                    'module'     => 'profile'
                ]
            )
        );

        $router->addRoute(
            'companies',
            new Zend_Controller_Router_Route(
                'companies/*',
                [
                    'controller' => 'company',
                    'action'     => 'index',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'companies_view',
            new Zend_Controller_Router_Route(
                'companies/view/:companyId',
                [
                    'controller' => 'company',
                    'action'     => 'view',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'hybridauth',
            new Zend_Controller_Router_Route(
                'hybridauth',
                [
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'hybridauth-endpoint'
                ]
            )
        );

        $router->addRoute(
            'add-ad',
            new Zend_Controller_Router_Route(
                'add-ad',
                [
                    'controller' => 'index',
                    'action'     => 'add-ad',
                    'module'     => 'default'
                ]
            )
        );

        $router->addRoute(
            'activation',
            new Zend_Controller_Router_Route(
                'activation/:code',
                [
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'activation'
                ]
            )
        );
        $route = new Zend_Controller_Router_Route_Regex(
            '([a-zA-Z0-9\-]+).html',
            [
                'controller' => 'page',
                'action'     => 'index',
                'module'     => 'default'
            ],
            [
                1 => 'url'
            ],
            '%s.html'
        );

        $router->addRoute(
            'subscription',
            new Zend_Controller_Router_Route(
                'subscription',
                [
                    'controller' => 'subscription',
                    'action'     => 'index',
                    'module'     => 'profile'
                ]
            )
        );

        $router->addRoute('slug', $route);

        $routes = $router->getRoutes();
        foreach ($routes as $name => $route) {
            $router->addRoute($name, $langRoute->chain($route));
        }

        $router->addRoute(
            'admin',
            new Zend_Controller_Router_Route(
                'admin/:controller/:action/*',
                [
                    'module'     => 'admin',
                    'controller' => 'index',
                    'action'     => 'index'
                ]
            )
        );

        $router->addRoute(
            'rssVacancies',
            new Zend_Controller_Router_Route(
                'rss/vacancies/',
                [
                    'module'     => 'default',
                    'controller' => 'rss',
                    'action'     => 'vacancies'
                ]
            )
        );

        $router->addRoute(
            'rssResumes',
            new Zend_Controller_Router_Route(
                'rss/resumes/',
                [
                    'module'     => 'default',
                    'controller' => 'rss',
                    'action'     => 'resumes'
                ]
            )
        );

        $router->addRoute(
            'rssAllads',
            new Zend_Controller_Router_Route(
                'rss/allads/',
                [
                    'module'     => 'default',
                    'controller' => 'rss',
                    'action'     => 'allads'
                ]
            )
        );

        $router->addRoute(
            'logout',
            new Zend_Controller_Router_Route(
                'logout',
                [
                    'controller' => 'auth',
                    'action'     => 'logout',
                    'module'     => 'admin'
                ]
            )
        );

        $router->addRoute(
            'sign-out',
            new Zend_Controller_Router_Route(
                'sign-out',
                [
                    'controller' => 'auth',
                    'action'     => 'logout',
                    'module'     => 'admin'
                ]
            )
        );
    }

    /**
     *
     * Initialization of main configuration
     * See configs/application.ini
     */
    protected function _initConfiguration()
    {
        $app    = $this->getApplication();
        $config = $app->getOptions();
        Zend_Registry::set('appConfig', $config);
        Zend_Locale::setDefault($config['locale'][$config['lang']]);
        date_default_timezone_set($config['timezone']);
        setlocale(LC_CTYPE, $config['locale'][$config['lang']]);
        if (APPLICATION_ENV == 'development') {
            error_reporting(E_ALL);
            ini_set("log_errors", 1);
            if (isset($config['phpsettings'])) {
                foreach ($config['phpsettings'] as $setting => $value) {
                    ini_set($setting, $value);
                }
            }
        }

        ini_set("error_log", APPLICATION_PATH . "/log/error.log");

        // get page from cache avoiding any further operations
        if (APPLICATION_ENV != 'development' && $config['cache']['pages']) {
            $this->_usePageCache();
        }
    }

    /**
     * CACHE FULL PAGE
     *
     * @return bool
     * @throws Zend_Exception
     */
    protected function _usePageCache()
    {
        $options = Zend_Registry::get('appConfig');

        // init pages caching
        $frontendOptions = [
            'lifetime'                  => 900, // 15 min hour
            // 'debug_header'              => true, // для отладки
            'content_type_memorization' => true,
            'default_options'           => [
                'cache'                        => true,
                'cache_with_session_variables' => true,
                'cache_with_cookie_variables'  => true,
            ],
            'automatic_serialization'   => true,
            'regexps'                   => [
                // кэширование всего IndexController
                '^/.*'     => ['cache' => true],
                '^/index/' => ['cache' => true],
                '^/page/'  => ['cache' => true],
                // place more controller links here to cache them
            ]
        ];
        $backendOptions  = [
            'servers'     => [
                ['host' => $options['memcached']['host'], 'port' => $options['memcached']['port']]
            ],
            'compression' => false
        ];

        $pagesCache = Zend_Cache::factory(
            'Page',
            'Memcached',
            $frontendOptions,
            $backendOptions
        );

        if (!$options['cache']['pages']) {
            $pagesCache->clean();

            return true;
        }

        if (APPLICATION_ENV == 'development' || !$options['cache']['pages']) {
            $pagesCache->clean();

            return true;
        }

        $pagesCache->start();
    }

    /**
     *
     * Initializing front controller
     */
    protected function _initController()
    {
        $this->bootstrap('FrontController');
        $controller = $this->getResource('FrontController');
        $modules    = $controller->getControllerDirectory();
        $controller->setParam('prefixDefaultModule', true);
        $controller->registerPlugin(new Zend_Controller_Plugin_ErrorHandler());
        $controller->registerPlugin(new Core_Modules_Loader($modules));
        $controller->registerPlugin(new Core_Controller_Plugin_Language());

        //$controller->throwExceptions(true);

        return $controller;
    }

    protected function _initAutoload()
    {
        require_once APPLICATION_PATH . '/../library/functions.php';
    }

    /**
     *
     * Initializing request
     */
    protected function _initRequest()
    {
        $this->bootstrap('FrontController');
        $front   = $this->getResource('FrontController');
        $request = $front->getRequest();
        if (null === $front->getRequest()) {
            $request = new Zend_Controller_Request_Http();
            $front->setRequest($request);
        }

        return $request;
    }

    protected function _initView()
    {
        $view = new Zend_View();
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->addHelperPath('Core/View/Helper/', 'Core_View_Helper');
        $view->addHelperPath('Core/View/Helper/Html', 'Core_View_Helper_Html');
        $view->addHelperPath('Core/View/Helper/Buttons', 'Core_View_Helper_Buttons');
        $view->addHelperPath('Core/View/Helper/Foundation', 'Core_View_Helper_Foundation');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );

        $viewRenderer->setView($view);

        return $view;
    }

    protected function _initLayout()
    {
        Zend_Layout::startMvc(
            [
                'layoutPath' => APPLICATION_PATH . '/layout/website',
                'layout'     => 'main',
            ]
        );
    }

    /**
     * Initializing view caching
     * See cache/Cache.php
     */
    protected function _initViewCaching()
    {
        $path              = APPLICATION_PATH . '/cache/';
        $classFileIncCache = $path . 'Cache.php';
        if (!file_exists($classFileIncCache)) {
            file_put_contents($classFileIncCache, '');
        }
        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        include_once $classFileIncCache;
    }

    /**
     * Init caching either in file system or memcached
     *
     * @throws Zend_Exception
     */
    protected function _initCaching()
    {
        $options = Zend_Registry::get('appConfig');

        // memcached support if available
        if (isset($options['memcached']) && class_exists('Memcached')) {
            $backend  = [
                'servers'     => [
                    ['host' => $options['memcached']['host'], 'port' => $options['memcached']['port']]
                ],
                'compression' => false
            ];
            $frontend = [
                'caching'                 => true,
                'lifetime'                => 1800,
                'automatic_serialization' => true,
                'cache_id_prefix'         => 'job',
            ];
            $cache    = Zend_Cache::factory(
                'Core',
                'Memcached',
                $frontend,
                $backend
            );
            Zend_Registry::set('cache', $cache);
        } else {
            $frontendOptions = [
                'lifetime'                => 25200,
                'automatic_serialization' => true
            ];
            $backendOptions  = [
                'cache_dir' => APPLICATION_PATH . '/cache'
            ];
            $cache           = Zend_Cache::factory(
                'Core',
                'File',
                $frontendOptions,
                $backendOptions
            );
            Zend_Registry::set('cache', $cache);
        }
        Zend_Translate::setCache($cache);
        //Cache table metadata

        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

        if (APPLICATION_ENV == 'development') {
            $cache->clean();
        }
    }

    /**
     * Initializing Db
     */
    protected function _initDatabase()
    {
        $options = Zend_Registry::get('appConfig');
        $db      = Zend_Db::factory($options['database']['adapter'], $options['database']['params']);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('DB', $db);

        return $db;
    }

    /**
     *
     * Building acl
     * Checking user identity permissions
     *
     * @throws Exception
     */
    protected function _initAcl()
    {
        $role = Core_Acl_AclBuilder::DEFAULT_ROLE_CODE;
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $user     = Core_Model_User::getInstance();
            $userData = $auth->getIdentity();
            if (!is_array($userData)) {
                $auth->clearIdentity();
                throw new Exception(Core_Messages_Message::getMessage(98));
            }
            $user->populate($auth->getIdentity());
            $role = $user->role;
        }

        $aclBuilder = new Core_Acl_AclBuilder();
        $aclBuilder->init();
        $aclBuilder->buildAcl($role);
        $rolesAcl = $aclBuilder->getRoleAcl();

        if ($auth->hasIdentity() && !array_key_exists($role, $rolesAcl)) {
            $user->clear();
            $auth->clearIdentity();
        }

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Core_Controller_Plugin_Acl($aclBuilder, $role));

        if (array_key_exists($role, $rolesAcl)) {
            Zend_Registry::set('acl', $rolesAcl[$role]);
            Zend_Registry::set('fullAcl', $rolesAcl);
            Zend_Registry::set('aclObject', $aclBuilder);
        } else {
            throw new Exception('No such role');
        }
    }

    protected function _initRenewSession()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return false;
        }

        if (Core_Model_User::getInstance()->timeout > 0) {
            $session = new Zend_Session_Namespace('Zend_Auth');
            $session->setExpirationSeconds(Core_Model_User::getInstance()->timeout);
        }
    }

    protected function _initSmpt()
    {
        $app    = $this->getApplication();
        $config = $app->getOptions();
        $tr     = new Zend_Mail_Transport_Smtp($config['mail']['server'], $config['mail']);
        Zend_Mail::setDefaultTransport($tr);
    }

    protected function _initModules()
    {
        // Call to prevent ZF from loading all modules
    }
}