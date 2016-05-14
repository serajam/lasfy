<?php

/**
 * Front Controller Plugin
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @subpackage Plugins
 */
class Core_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $acl = Zend_Registry::get('acl');
        if (empty($acl)) {
            return false;
        }

        $normalizedAcl = (Core_Controller_Plugin_AclNormalizer::normalize($acl));
        if (empty($normalizedAcl)) {
            return false;
        }
        $container  = new Zend_Navigation();
        $pagesArray = [];
        foreach ($normalizedAcl as $module => $Resources) {
            if (empty($Resources['Resources']) || count($Resources['Resources']) == 0) {
                continue;
            }
            if (!array_key_exists('show', $Resources) || $Resources['show'] == 0) {
                continue;
            }
            $pagesArray[$module] = ['label' => $Resources['moduleName'], 'module' => $module];
            foreach ($Resources['Resources'] as $controller => $action) {

                $pagesArray[$module]['pages'][$controller] = [
                    'label'      => $controller,
                    'controller' => $controller,
                    'module'     => $module,
                ];
                foreach ($action as $val) {
                    $visible = false;
                    if ($val['menu']) {
                        $visible = true;
                    }
                    $pagesArray[$module]['pages'][$controller]['pages'][] = [
                        'label'      => $val['name'],
                        'action'     => $val['action'],
                        'controller' => $controller,
                        'module'     => $module,
                        'visible'    => $visible,
                    ];
                }
            }
        }
        $container->addPages($pagesArray);
        Zend_Registry::set('navigation', $container);
    }
}
