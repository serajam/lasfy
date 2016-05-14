<?php

/**
 *
 * The collection of Domain objects
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class TranslationsCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Translation';

    public function generateInputs()
    {
        if ($this->count() < 1) {
            return false;
        }

        $conf  = Zend_Registry::get('languages');
        $langs = $conf['languages'];

        $htmlInputs = '<table class="default-table"><thead><tr><th>Код</th>';

        foreach ($langs as $l => $val) {
            $htmlInputs .= '<th>' . $val . '</th>';
        }
        $htmlInputs .= '</tr></thead><tbody>';

        $sortedByLang = [];
        foreach ($this as $trans) {
            $sortedByLang[$trans->code][$trans->lang] = $trans;
        }

        foreach ($sortedByLang as $code => $trans) {
            $htmlInputs .= '<tr>';
            $htmlInputs .= '<td>' . $code . '</td>';
            foreach ($langs as $l => $val) {

                if (array_key_exists($l, $trans)) {
                    $obj     = $trans[$l];
                    $caption = htmlspecialchars(($obj->caption));

                    $htmlInputs .= '<td><input name="trans[' . $code . '][' . $l . ']" type="text" value="'
                        . $caption . '" /></td>';
                } else {
                    $htmlInputs .= '<td><input name="trans[' . $code . '][' . $l . '][new]" type="text" value="" /></td>';
                }
            }
            $htmlInputs .= '</tr>';
        }
        $htmlInputs .= '</tbody></table>';

        return $htmlInputs;
    }
}