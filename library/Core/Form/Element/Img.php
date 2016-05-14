<?php

class Core_Form_Element_Img extends Zend_Form_Element_Xhtml
{
    public $helper = 'formImg';

    public function loadDefaultDecorators()
    {
        parent::loadDefaultDecorators();
        $this->removeDecorator('Label');
        $this->removeDecorator('HtmlTag');

        $this->addDecorator('HtmlTag', [
            'tag'   => 'span',
            'class' => 'image',
        ]);
    }
}