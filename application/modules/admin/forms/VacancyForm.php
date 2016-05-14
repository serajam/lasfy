<?php

/**
 * Class VacancyForm
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class VacancyForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'vacancy');
        $this->addElement(
            'hidden',
            'vacancyId',
            [
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 10]]
                ]
            ]
        );
        $this->addSubmit('save');
    }
}
