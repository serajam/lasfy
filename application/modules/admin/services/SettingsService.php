<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 27.08.13
 * Time: 20:29
 * To change this template use File | Settings | File Templates.
 */
class SettingsService extends Core_Service_Editor
{
    /**
     * @var SettingsMapper
     */
    protected $_mapper;

    /**
     * Mapper class name
     *
     * @var string
     */
    protected $_mapperName = 'SettingsMapper';

    protected $_validatorName = 'SettingsForm';

    public function getValidator($id = false)
    {
        return $this->getSettingsForm();
    }

    public function getSettingsForm()
    {
        if ($this->getFormLoader()->isExists('SettingsForm')) {
            return $this->getFormLoader()->getForm('SettingsForm');
        }
        $form  = $this->getFormLoader()->addForm('SettingsForm');
        $types = Zend_Registry::get('types');
        $empty = [0 => __('make_choose')];
        $form->getElement('paramGroup')->addMultiOptions($empty + $types['paramGroups']);

        return $form;
    }
}