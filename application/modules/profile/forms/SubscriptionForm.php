<?php

/**
 * Class SubscriptionForm
 *
 * @author Fedor Petryk
 */
class SubscriptionForm extends Core_Form
{
    public function init()
    {
        $this->setAttrib('class', 'form');
        $this->setAttrib('id', 'subscriptionForm');

        $this->addElement(
            'hidden',
            'process',
            [
                'required' => true,
                'value'    => 'subscribe',
                'filters'  => ['StringTrim', 'StripTags'],

            ]
        );

        $this->addElement(
            'hidden',
            'subscriptionId',
            [
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => ['int']
            ]
        );

        $this->addElement(
            'select',
            'period',
            [
                'label'      => __('forPeriod'),
                'required'   => true,
                'class'      => 'has-tip tip-top',
                'title'      => __('period'),
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    'int',
                    ['StringLength', false, [1, 3]],
                ]
            ]
        );

        $this->addElement(
            'checkbox',
            'active',
            [
                'checked'    => '',
                'value'      => '1',
                'label'      => __('subscriptionActive'),
                'required'   => true,
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    'int',
                    ['StringLength', false, [0, 2]],
                ]
            ]
        );
        $this->getElement('active')->setChecked(true);

        $this->addElement(
            'checkbox',
            'onlyNew',
            [
                'label'      => __('onlyNewAdds'),
                'required'   => true,
                'title'      => __('myExpectation'),
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    'int',
                    ['StringLength', false, [0, 1]],
                ]
            ]
        );

        $this->addElement(
            'select',
            'type',
            [
                'label'      => __('addsType'),
                'required'   => true,
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    'int',
                    ['StringLength', false, [0, 1]],
                ]
            ]
        );

        $this->addTagsElement();

        $this->addSubmit('save', 'sub', 'button');

        $this->addDisplayGroup(
            [
                'subscriptionId',
                'tags',
                'period',
                'active',
                'onlyNew',
                'type',
                'sub',
                'process',
            ],
            'subscriptionGroup',
            ['disableLoadDefaultDecorators' => true]
        );
        $this->getDisplayGroup('subscriptionGroup')
            ->addDecorators(
                [
                    'FormElements',
                    'Fieldset',
                    ['HtmlTag', ['tag' => 'div', 'class' => 'columns panel']]
                ]
            );
    }

    protected function addTagsElement()
    {
        $this->addElement(
            'textarea',
            'tags',
            [
                'placeholder' => __('tags'),
                'label'       => __('tags'),
                'required'    => true,
                'filters'     => ['StringTrim', 'StripTags'],
                'validators'  => [
                    ['StringLength', false, [2, 1000]],
                ],
                'class'       => 'tagsSet'
            ]
        );
    }

    protected function _addFilters()
    {
    }
}