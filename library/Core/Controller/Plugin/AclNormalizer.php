<?php

/**
 *
 * The ACL normalizer class
 * Builds tree array according to required module and style
 *
 * @author Fedor Petryk
 *
 */
class Core_Controller_Plugin_AclNormalizer extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * Default result ACL tree array
     *
     * @var array
     */
    protected static $_acl = null;

    /**
     *
     * Builds tree for View_Helper_Users
     *
     * @param Acl registered in Zend_Registry under "acl" | $acl
     */
    public static function normalize(array $acl)
    {
        foreach ($acl as $a) {
            if (!empty($a['resourceCode'])) {
                list ($module, $resourse) = explode(':', $a ['resourceCode']);
                self::$_acl [$module] ['moduleName'] = $a ['moduleName'];
                self::$_acl [$module] ['roleName']   = $a ['roleName'];
                self::$_acl [$module] ['show']       = $a ['show'];
                self::$_acl [$module] ['id']         = $a ['id'];
                self::$_acl [$module] ['Resources'] [$resourse] []
                                                     = [
                    'action' => $a ['action'],
                    'name'   => $a ['rightName'],
                    'menu'   => $a ['menu']
                ];
            }
        }

        return self::$_acl;
    }

    /**
     *
     * Builds tree by Roles
     *
     * @param Acl registered in Zend_Registry under "acl" | $acl
     */
    public static function normalizeByRole(array $acl)
    {
        $fullAcl = [];
        foreach ($acl as $role => $element) {
            foreach ($element as $a) {
                if (!empty($a['resourceCode'])) {
                    $fullAcl[$role]['roleName'] = $a['roleName'];
                    $fullAcl[$role]['roleId']   = $a['roleId'];
                    $fullAcl[$role]['roleCode'] = $a['roleCode'];
                    $fullAcl[$role]['editable'] = $a['editable'];

                    list($module, $resourse) = explode(':', $a['resourceCode']);
                    $fullAcl[$role]['modules'][$module]['moduleName'] = $a['moduleName'];

                    $fullAcl[$role]['modules'][$module]['Resources'][$resourse][] = [
                        'action' => $a['action'],
                        'name'   => $a['rightName']
                    ];
                }
            }
        }

        return $fullAcl;
    }

    /**
     *
     * Builds full Acl tree
     *
     * @param Acl registered in Zend_Registry under "acl" | $acl
     */
    public static function fullAclNormalize(array $acl)
    {
        $fullAcl = [];
        foreach ($acl as $role => $element) {
            foreach ($element as $a) {
                if (!empty($a['resourceCode'])) {
                    list($module, $resourse) = explode(':', $a['resourceCode']);

                    $fullAcl['modules'][$module]['moduleName'] = $a['moduleName'];
                    $fullAcl['modules'][$module]['Resources'][$a['resourceCode']]['actions']
                    [$a['action']]
                                                               = $a['rightName'];
                }
            }
        }

        return $fullAcl;
    }

    /**
     *
     * Returns acl
     *
     * @return array
     */
    public static function getAcl()
    {
        if (null != self::$_acl) {
            return self::$_acl;
        }
    }
}