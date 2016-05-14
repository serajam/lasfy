<?php

/**
 *
 * Role row class
 *
 * @author     Fedor Petryk
 *
 */
class Role extends Core_Model_Super
{
    protected $_data = [
        'roleId'            => null,
        'roleName'          => null,
        'roleCode'          => null,
        'editable'          => 1,
        'defaultModule'     => null,
        'defaultController' => null,
        'defaultAction'     => null,
        'active'            => null
    ];

    /**
     *
     * Array of role rules
     *
     * @var array
     */
    protected $_rules = [];

    public function getRules()
    {
        return $this->_rules;
    }

    /**
     *
     * Setting role rules
     *
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->_rules = $rules;
    }
}