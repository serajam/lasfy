<?php

/**
 *
 * Role Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class AdminService extends Core_Service_Editor
{
    /**
     * @var AdminMapper
     */
    protected $_mapper;

    /**
     *
     * Current service domain object
     *
     * @var Object
     */
    protected $_rowObject = null;

    /**
     *
     * Role mapper class
     *
     * @var String
     */
    protected $_mapperName = 'AdminMapper';

    /**
     *
     * THe validator - role form class name
     *
     * @var String
     */
    protected $_validatorName = 'RoleForm';

    /**
     *
     * THe validator - role form class object
     *
     * @var RoleForm
     */
    protected $_validator;

    /**
     *
     * Get role object
     *
     * @param int $id | role id
     */
    public function getRole($id)
    {
        $this->_rowObject = $this->_mapper->getRole($id);
        $this->_validator->populate($this->_rowObject->toArray());
    }

    public function getObject()
    {
        return $this->_rowObject;
    }

    /**
     *
     * Get full acl list by modules
     *
     * @return mixed | ACL or boolean
     *
     */
    public function getFullAcl()
    {
        $acl         = $this->_mapper->getFullAcl();
        $aclByModule = [];
        if (!empty($acl)) {
            foreach ($acl as $a) {
                $aclByModule[$a['id']]['moduleName'] = $a['moduleName'];

                $aclByModule[$a['id']]['Resources'][$a['resourceId']]['resourceName'] = $a['resourceName'];

                if (!empty($a['rightId'])) {
                    $aclByModule[$a['id']]['Resources'][$a['resourceId']]['actions']
                    [$a['rightId']]['rightName'] = $a['rightName'];
                }
            }

            return $aclByModule;
        }

        return false;
    }

    /**
     *
     * Creates new role with assigned Resources and Rights
     *
     * @param array | Massive of role data
     *
     * @return mixed | boolean or Role
     */
    public function saveRole(array $data)
    {

        if (!$this->getValidator()->isValid($data)) {
            $this->setError(Core_Messages_Message::getMessage(300));

            return false;
        }

        if (!$this->_validateRules($data)) {
            $this->setError(Core_Messages_Message::getMessage('resource_or_action_is_empty'));

            return false;
        }

        $class = $this->_mapper->getRowClass();
        $model = new $class;
        $model->populate($data);

        if ($model->roleId != null) {
            $this->_mapper->deleteAllRules($model->roleId);
        }

        $this->_mapper->objectSave($model);
        if ($model->getError()) {
            $this->_error = $model->getError();

            return false;
        }
        $this->_mapper->addRules($data['actions'], $model);
        if ($model->roleId == null) {
            $this->_mapper->getDbTable()->deleteById($model->roleId);
            $this->_error = Core_Messages_Message::getMessage(300);

            return false;
        }
        $this->setMessage(Core_Messages_Message::getMessage(1));

        return $model;
    }

    /**
     *
     * Validating rules and building queries
     *
     * @param $data | rules array
     *
     * @return boolean
     */
    private function _validateRules(array $data)
    {
        if (empty($data['Resources']) || empty($data['actions'])) {
            return false;
        }

        return true;
    }
}