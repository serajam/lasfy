<?php

/**
 *
 * Page Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class PagesService extends Core_Service_Editor
{
    /**
     *
     * Role mapper class
     *
     * @var String
     */
    protected $_mapperName = 'PagesMapper';

    /**
     * @var PagesMapper
     */
    protected $_mapper;

    /**
     *
     * THe validator - role form class name
     *
     * @var String
     */
    protected $_validatorName = 'PageForm';

    /**
     * @param array $post
     * @param null  $id
     *
     * @return bool
     * @throws Exception
     * @throws Zend_Form_Exception
     *
     * @author Fedor Petryk
     */
    public function edit($post, $id = null)
    {
        if (!$this->getValidator()->isValid($post)) {
            $this->_processFormError($this->getValidator());

            return false;
        }

        $values = $this->getValidator()->getValues();

        // find editable page and populate existing values with new
        if ($id != null) {
            $model = $this->_mapper->getPage(null, $id, $post['lang']);
            if (empty($model)) {
                $this->setError(Core_Messages_Message::getMessage('error'));

                return false;
            }
            $model->populate($values);
        }
        // if creating new page, try to find same page with slug
        // if there is one, create new page for lang with the same id
        elseif (!$id) {
            $className = $this->_mapper->getRowClass();
            $model     = new $className;
            $model->populate($values);
            $langPage = $this->_mapper->getPage($post['slug'], null, null);

            if ($langPage) {

                if ($langPage->lang == $model->lang) {
                    $this->setError(Core_Messages_Message::getMessage('page_with_slug_and_lang_exists'));

                    return false;
                }

                $model->pageId = $langPage->pageId;
            }
        } else {
            $this->setError(Core_Messages_Message::getMessage('record_not_found'));

            return false;
        }

        $this->_postData = $post;

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
    }

    /**
     * @param bool|false $id
     *
     * @return Core_Form
     *
     * @author Fedor Petryk
     */
    public function getValidator($id = false)
    {
        return $this->getPageForm();
    }

    /**
     * @return Core_Form
     * @throws Zend_Exception
     *
     * @author Fedor Petryk
     */
    public function getPageForm()
    {
        if ($this->getFormLoader()->isExists('PageForm')) {
            return $this->getFormLoader()->getForm('PageForm');
        }
        $form  = $this->getFormLoader()->addForm('PageForm');
        $types = Zend_Registry::get('types');
        $empty = [0 => __('make_choose')];
        $form->getElement('lang')->addMultiOptions($empty + array_flip($types['language']));
        $trans = Zend_Registry::get('translation');
        $form->getElement('type')->addMultiOptions($empty + $trans->translatePairs($types['cct']));

        return $form;
    }

    /**
     * @param $model
     *
     * @return bool
     *
     * @author Fedor Petryk
     */
    public function _preSave($model)
    {
        $model->dateChanged = Zend_Date::now()->toString('yyyy-MM-dd H:i:s');
        if (empty($model->slug)) {
            $model->slug = null;
        }

        return true;
    }

    /**
     * @return Core_Collection_Menu
     */
    public function getMenuList()
    {
        $lang = Core_Settings_Settings::getDefaultLanguage();

        return $this->_mapper->getMenu($lang);
    }

    /**
     * @param      $post
     * @param null $id
     *
     * @return bool
     * @throws Exception
     *
     * @author Fedor Petryk
     */
    public function editMenu($post, $id = null)
    {
        $form = $this->getMenuForm();
        if (array_key_exists('type', $post)) {
            if (in_array($post['type'], [1, 2])) {
                $form->getElement('contentId')->setRequired(true);
                $form->getElement('lang')->setRequired(true);
            } elseif ($post['type'] == 3) {
                $form->removeElement('contentId');
                $form->getElement('lang')->setRequired(true);
            } elseif ($post['type'] == 4 || $post['type'] == 5) {

                $form->getElement('contentId')->setRequired(true);
                $form->getElement('lang')->setRequired(true);
            }
        }
        if (!$form->isValid($post)) {
            $this->_processFormError($form);

            return false;
        }
        $this->_mapper->setRowClass('Core_Model_Menu')->setDbTable('Core_DbTable_Menu');
        $data = $form->getValues();

        if (!$data['parentId']) {
            $this->_mapper->reOrderMenu($data['position'], $data['lang']);
        } else if ($data['parentId']) {
            $menuItem         = $this->_mapper->getMenuItem($data['parentId']);
            $data['position'] = $menuItem['position'];
        }

        $menu = $this->save($data);
        if ($menu->getError()) {
            $this->setError($menu->getError());

            return false;
        }

        if ($post['type'] == 4 || $post['type'] == 5) {
            if (isset($post['pageId'])) {
                $this->_mapper->addMenuPages($menu->menuId, $post['pageId']);
            }
        }

        $this->setMessage(__(1));

        return true;
    }

    /**
     * @return Core_Form
     * @throws Zend_Exception
     *
     * @author Fedor Petryk
     */
    public function getMenuForm()
    {
        if ($this->getFormLoader()->isExists('MenuForm')) {
            return $this->getFormLoader()->getForm('MenuForm');
        }
        $trans          = Zend_Registry::get('translation');
        $form           = $this->getFormLoader()->addForm('MenuForm');
        $types          = Zend_Registry::get('types');
        $empty          = [0 => __('make_choose')];
        $menuTypes      = $types['menuType'];
        $availabilities = $types['availability'];
        $form->getElement('lang')->addMultiOptions($empty + $trans->translatePairs(array_flip($types['language'])));
        $form->getElement('type')->addMultiOptions($empty + $trans->translatePairs(array_flip($menuTypes)));
        $form->getElement('availability')->addMultiOptions($empty + $trans->translatePairs(array_flip($availabilities)));
        $form->getElement('parentId')->addMultiOptions($empty + $this->_mapper->getMenuIdNamePair());
        $position = 1;
        if ($this->_currentCollection) {
            $position = $this->_currentCollection->count();
        }
        $form->getElement('position')->setValue($position);
        // @TODO get content undependable from language
        $content = $this->_mapper->getContentList(2);
        $form->getElement('contentId')->setMultiOptions($empty + $content);
        $form->getElement('pageId')->setMultiOptions($content);

        return $form;
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws Exception
     *
     * @author Fedor Petryk
     */
    public function deleteMenu($id)
    {
        $oldRowClass = $this->_mapper->getRowClass();
        $this->_mapper->setRowClass('Core_Model_Menu')->setDbTable('Core_DbTable_Menu');
        $res = parent::delete($id);
        $this->_mapper->setDefaultDbTable()->setRowClass($oldRowClass);

        return $res;
    }

    /**
     * @param $id
     *
     * @author Fedor Petryk
     */
    public function getMenuJson($id)
    {
        $lang            = Core_Settings_Settings::getDefaultLanguage();
        $this->_jsonData = $this->_mapper->getMenuItem($id, $lang);
    }

    /**
     * @param $id
     * @param $slug
     * @param $lang
     *
     * @return bool
     *
     * @author Fedor Petryk
     */
    public function getPageJson($id, $slug, $lang)
    {
        $model = $this->_mapper->getPage(null, $id, $lang, true);

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

    /**
     * @return Core_Form
     * @throws Zend_Exception
     *
     * @author Fedor Petryk
     */
    public function getPagesFilterForm()
    {
        $form       = $this->getFormLoader()->getForm('PagesFilterForm');
        $types      = Zend_Registry::get('types');
        $langs      = $types['language'];
        $pagesTypes = $types['pageType'];
        $empty      = [0 => __('make_choose')];

        $form->getElement('lang')
            ->addMultiOptions($empty + $langs);

        $form->getElement('pageType')
            ->addMultiOptions($empty + $pagesTypes);

        return $form;
    }

    /**
     * @param $data
     * @param $page
     *
     * @return Core_Collection_Super|Core_Model_Collection_Super|mixed
     *
     * @author Fedor Petryk
     */
    public function getPagesCollection($data, $page)
    {
        if (empty($data)) {
            return parent::getCollection(false, false, $page);
        }

        $types        = Zend_Registry::get('types');
        $langs        = $types['language'];
        $data['lang'] = isset($langs[$data['lang']]) ? $langs[$data['lang']] : 1;

        return $this->_mapper->fetchAllWhereWithPage($data, $page);
    }

    /**
     * Delete page by slug, id, lang
     *
     * @param $post
     * @param $id
     *
     * @return bool
     * @author Fedor Petryk
     */
    public function deletePage($post, $id)
    {
        $data = $this->_mapper->getPage($post['slug'], $id, $post['lang']);
        if (empty($data)) {
            $this->setError(Core_Messages_Message::getMessage('record_not_found'));

            return false;
        }

        $result = $this->getMapper()->getDbTable()->deleteByPrimaryKey($id, $post['lang'], $post['slug']);
        if ($result) {
            $this->setMessage(Core_Messages_Message::getMessage('record_deleted'));

            return true;
        } else {
            $this->setMessage(Core_Messages_Message::getMessage('record_delete_failed'));

            return false;
        }
    }
}