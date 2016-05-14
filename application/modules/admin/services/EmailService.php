<?php

/**
 *
 * Finance and statistics Service class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class EmailService extends Core_Service_Editor
{
    protected $_mapperName = 'EmailMapper';

    protected $_validatorName = 'EmailTemplateForm';

    /**
     * @var EmailMapper
     */
    protected $_mapper;

    public function getValidator($id = false)
    {
        return $this->getEmailTemplateForm();
    }

    public function getEmailTemplateForm()
    {
        $form = $this->getFormLoader()->getForm('EmailTemplateForm');

        $types     = Zend_Registry::get('types');
        $langs     = $types['language'];
        $langCodes = [];
        foreach ($langs as $langCode => $id) {
            $langCodes[$langCode] = $langCode;
        }
        $form->getElement('lang')->addMultiOptions($langCodes);

        return $form;
    }
}