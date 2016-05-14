<?php

class Core_View_Helper_FormImg extends Zend_View_Helper_FormElement
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function formImg($name, $value, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);

        $xHtml = '<img'
            . $this->_htmlAttribs($attribs)
            . ' />';

        return $xHtml;
    }
}