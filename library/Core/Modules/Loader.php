<?php

/**
 *
 * The modue loader class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Modules_Loader extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * The list of the modules
     *
     * @var String
     */
    protected $_modules;

    /**
     * @var array
     **/
    protected $_errorPage;

    /**
     *
     * Assigns current module list
     *
     * @param array $modulesList
     */
    public function __construct(array $modulesList)
    {
        $this->_errorPage = [
            'module'     => 'default',
            'controller' => 'error',
            'action'     => 'denied'
        ];
        $this->_modules   = $modulesList;
    }

    /**
     * Initializes requested module bootstrap
     *
     * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::dispatchLoopStartup()
     *
     * @param Zend_Controller_Request_Abstract | $request
     *
     * NOTE: changed from dispatchLoopStartup to routeShutdown because need to checksystem availabilty
     * before the initializaion module controllers
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();

        if (!isset($this->_modules[$module])) {
            $request->setModuleName($this->_errorPage['module']);
            $request->setControllerName($this->_errorPage['controller']);
            $request->setActionName($this->_errorPage['action']);

            return false;
        }

        // checks if module is not blocked by security system
        $isBlocked = Core_Modules_Blocker::isBlocked($module);
        if ($isBlocked) {
            $params = ['0' => $module];
            $request->setModuleName($this->_errorPage['module']);
            $request->setControllerName($this->_errorPage['controller']);
            $request->setActionName($this->_errorPage['action']);

            return false;
        }

        $bootstrapPath = $this->_modules[$module];

        $bootstrapFile = dirname($bootstrapPath) . '/Bootstrap.php';
        $class         = ucfirst($module) . '_Bootstrap';
        $application   = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/modules/' . $module . '/configs/module.ini'
        );

        if (Zend_Loader::loadFile('Bootstrap.php', dirname($bootstrapPath))
            && class_exists($class)
        ) {
            $bootstrap = new $class($application);
            $bootstrap->bootstrap();
        }
    }
}