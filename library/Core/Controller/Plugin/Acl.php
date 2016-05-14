<?php

/**
 * Front Controller Plugin
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Zion
 * @package    Zion_Controller
 * @subpackage Plugins
 */
class Core_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Acl
     **/
    protected $_acl;

    /**
     * @var string
     **/
    protected $_roleName;

    /**
     * @var array
     **/
    protected $_errorPage;

    public function __construct(Core_Acl_AclBuilder $aclData, $roleName = 'defaultRole')
    {
        $this->_errorPage = [
            'module'     => 'default',
            'controller' => 'error',
            'action'     => 'denied'
        ];

        $this->_roleName = $roleName;

        if (null !== $aclData) {
            $this->setAcl($aclData);
        }
    }

    /**
     * Returns the ACL role used
     *
     * @return string
     * @author
     **/
    public function getRoleName()
    {
        return $this->_roleName;
    }

    /**
     * Sets the ACL role to use
     *
     * @param string $roleName
     *
     * @return void
     **/
    public function setRoleName($roleName)
    {
        $this->_roleName = $roleName;
    }

    /**
     * Returns the error page
     *
     * @return array
     **/
    public function getErrorPage()
    {
        return $this->_errorPage;
    }

    /**
     * Sets the error page
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     *
     * @return void
     **/
    public function setErrorPage($action, $controller = 'error', $module = 'default')
    {
        $this->_errorPage = [
            'module'     => $module,
            'controller' => $controller,
            'action'     => $action
        ];
    }

    /**
     * routeShutdown
     * Checks if the current user identified by roleName has rights to the requested url (module/controller/action)
     * If not, it will call denyAccess to be redirected to errorPage
     *
     * @var Zend_Controller_Request_Abstract $request
     * @return void
     **/
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $resourceName = '';
        $resourceName .= $request->getModuleName() . ':';
        $resourceName .= $request->getControllerName();
        $module = $request->getModuleName();
        if (!$this->getAcl()->has($resourceName)) {
            $this->setErrorPage('no-page', 'error', 'default');
            $this->denyAccess();

            return false;
        }

        $auth = Zend_Auth::getInstance();
        /** Check if the controller/action can be accessed by the current user */
        if (!$this->getAcl()->isAllowed($this->_roleName, $resourceName, $request->getActionName())) {
            if ($auth->hasIdentity()) {
                $this->denyAccess();
            } else {
                if (!strcmp($module, 'admin')) {
                    $this->setErrorPage('login', 'auth', 'admin');
                } else {
                    $this->setErrorPage('sign-in', 'index', 'default');
                }
                $this->denyAccess();
            }
            /** Redirect to access denied page */
        }
    }

    /**
     * Returns the ACL object
     *
     * @return Zend_Acl
     **/
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Sets the ACL object
     *
     * @param mixed $aclData
     *
     * @return void
     **/
    public function setAcl(Core_Acl_AclBuilder $aclData)
    {
        $this->_acl = $aclData;
    }

    /**
     * Deny Access Function
     * Redirects to errorPage, this can be called from an action using the action helper
     *
     * @return void
     **/
    public function denyAccess()
    {
        $this->_request->setModuleName($this->_errorPage['module']);
        $this->_request->setControllerName($this->_errorPage['controller']);
        $this->_request->setActionName($this->_errorPage['action']);
    }
}