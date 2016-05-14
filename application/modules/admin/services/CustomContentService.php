<?php

/**
 *
 * Page Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class CustomContentService extends Core_Service_Editor
{
    /**
     *
     * Role mapper class
     *
     * @var String
     */
    protected $_mapperName = 'CustomContentMapper';

    /**
     * @var CustomContentMapper
     */
    protected $_mapper;

    /**
     *
     * THe validator - role form class name
     *
     * @var String
     */
    protected $_validatorName = 'CustomContentForm';

    /**
     *
     * Fill validator with neccecary info data
     *
     * @return void
     *
     */
    protected function _fillForm()
    {
        $form  = $this->_validator;
        $types = Zend_Registry::get('types');
        $empty = [0 => __('make_choose')];
        $trans = Zend_Registry::get('translation');
        $form->getElement('type')->addMultiOptions($empty + $trans->translatePairs($types['cct']));

        return $form;
    }
}