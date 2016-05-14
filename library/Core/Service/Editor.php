<?php

/**
 *
 * Tenders Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_Service_Editor extends Core_Service_Super
{
    /**
     * @var Core_Mapper_Super
     */
    protected $_mapper;

    /**
     * @var C
     */
    protected $_listDecorator;

    /**
     * @var array
     */
    protected $_postData = [];

    /**
     * @var Core_Model_Super
     */
    protected $_model;

    /**
     * Current items collection
     *
     * @var Core_Collection_Super
     */
    protected $_currentCollection;

    /**
     * @return \Core_Collection_Super
     */
    public function getCurrentCollection()
    {
        return $this->_currentCollection;
    }

    public function setup($dbTable, $validator, $rowClass)
    {
        $this->_mapper->setDbTable($dbTable);
        $this->setValidator($validator);
        $this->_mapper->setRowClass($rowClass);
    }

    public function processMessages()
    {
        if ($this->getMailer()->isError()) {
            $this->setError($this->getMailer()->getError());
        }
        $this->setMessage($this->getMailer()->getMessage());
    }

    /**
     * @return Core_Mailer_Service_Super
     */
    public function getMailer()
    {
        if ($this->_mailer == null) {
            $this->_mailer = new Core_Mailer_Service_Super();
        }

        return $this->_mailer;
    }

    public function getJson($id)
    {
        $model = $this->getCurrentModel($id);

        if (!$this->isAccessible($model)) {
            $this->setError('no_access');

            return false;
        }
        if (empty($model)) {
            $this->setError('no_data');

            return false;
        }

        $this->_jsonData = $model->toArray();

        return true;
    }

    public function getCurrentModel($id)
    {
        if ($this->_model == null) {
            $this->_model = $this->get($id);
        }

        return $this->_model;
    }

    public function get($id)
    {
        $model = $this->_mapper->fetchById($id);
        if (!empty($model)) {
            $this->_fillModel($model);
        }

        return $model;
    }

    protected function isAccessible($model)
    {
        return true;
    }

    /**
     * @param array $post
     * @param null  $id
     *
     * @return bool|Core_Model_Super
     */
    public function edit($post, $id = null)
    {
        if ($id != null) {
            $data = $this->getCurrentModel($id);
            if (empty($data)) {
                $this->setError(Core_Messages_Message::getMessage('error'));

                return false;
            }
            $data->populate($post);
            $post = $data->toArray();
        }

        if ($this->getValidator()->isValid($post)) {
            $this->_postData = $post;
            $values          = $this->getValidator()->getValues();
            $modelClass      = $this->_mapper->getRowClass();
            $model           = new $modelClass;
            $model->populate($values);
            if (!$this->_validation($model)) {
                return false;
            }

            if (!$this->_preSave($model)) {
                return false;
            }
            $model = $this->save($model);

            if ($model->getError()) {
                $this->setError($model->getError());

                return false;
            }

            if (!$this->_postSave($model)) {
                return false;
            }

            $this->setMessage(Core_Messages_Message::getMessage(1));
            $this->_validator->populate($model->toArray());

            $this->setJsonData(['idKey' => $model->getFirstPropertyKey(), 'idVal' => $model->getFirstProperty()]);
        } else {
            $this->_processFormError($this->getValidator());

            return false;
        }
    }

    protected function _validation($model)
    {
        return true;
    }

    protected function _preSave($model)
    {
        return true;
    }

    protected function _postSave($model)
    {
        return true;
    }

    public function getByFields(array $fields)
    {
        $model = $this->_mapper->fetchByFields($fields);
        if (!empty($model)) {
            $this->_fillModel($model);
        }

        return $model;
    }

    public function getCollection($order = false, $dir = false, $page = false)
    {
        if ($order != false) {
            $rows = $this->_mapper->fetchAllOrdered($order, $dir);
        } else {
            $rows = $this->_mapper->fetchAll($page);
        }

        return $rows;
    }

    public function delete($id)
    {
        $model = $this->_mapper->fetchById($id);
        if (!empty($model)) {
            if (!$this->_preDelete($model)) {
                $this->setError('cant_delete');

                return false;
            }
            $res = $this->_mapper->getDbTable()->deleteById($id);
            if ($res != false) {
                $this->setError(Core_Messages_Message::getMessage($res));

                return false;
            }
            $this->setMessage(Core_Messages_Message::getMessage(1));
        } else {
            $this->setError('cant_fetch_model');
        }
    }

    protected function _preDelete($model)
    {
        return true;
    }

    /**
     * @param  $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
    }

    /**
     * @return
     */
    public function getPage()
    {
        return $this->_page;
    }
}
