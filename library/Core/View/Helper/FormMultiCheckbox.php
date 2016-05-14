<?php

/**
 * Multicheckbox with two column
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_FormMultiCheckbox extends Zend_View_Helper_FormMultiCheckbox
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function formMultiCheckbox(
        $name,
        $value = null,
        $attribs = null,
        $options = null,
        $listsep = "<br />\n"
    )
    {
        // zend_form_element attrib has higher precedence
        if (isset($attribs['listsep'])) {
            $listsep = $attribs['listsep'];
        }

        // Store original separator for later if changed
        $origSep = $listsep;

        // Don't allow whitespace as a seperator
        $listsep = trim($listsep);

        // Force a separator if empty
        if (empty($listsep)) {
            $listsep = $attribs['listsep'] = "<br />\n";
        }

        $string     = $this->formRadio($name, $value, $attribs, $options, $listsep);
        $checkboxes = explode($listsep, $string);

        $html = '';
        // Your code
        if (count($checkboxes) > 5) {
            $columns = array_chunk($checkboxes, count($checkboxes) / 2); //two columns
        } else {
            $columns = [$checkboxes];
        }

        foreach ($columns as $columnOfCheckboxes) {
            $html .= '<div style="float:left;">';

            $html .= implode($origSep, $columnOfCheckboxes);

            $html .= '</div>';
        }

        return $html;
    }
}
     