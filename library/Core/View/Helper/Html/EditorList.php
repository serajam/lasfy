<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Html_EditorList extends Core_View_Helper_View
{
    public static $buildAdditionalFields = false;

    public static $editable = false;

    public static $numbering = false;

    public static $checkbox = false;

    public static $mainCheckbox = false;

    public static $checked = false;

    public static $class = 'scroll';

    public static $id = '';

    public static $tableName = '';

    public static $width;

    public static $key = '';

    public static $actionsPosition = 'first';

    static public function buildActions($actions, $item)
    {
        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view');

        return static::_buildActions($actions, $item, $view);
    }

    /**
     * @param  Core_Model_Collection_Super $collection
     * @param bool                         $actions
     * @param array                        $fields
     * @param Core_Form                    $validator | Form Model
     *
     * @return string
     */
    public function editorList($collection, $actions = false, $fields = false, $validator = null, $highlight = false)
    {
        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view');

        if (empty($collection) || !is_object($collection) || !$collection->count()) {
            return false;
        }

        $collection->rewind();
        $tableHtml = '<div class="table-container ' . self::$class . '">';
        $tableHtml .= '<table class="responsive default-table"';
        if (self::$width) {
            $tableHtml .= ' width="' . self::$width . '"';
        }
        $tableHtml .= (self::$id ? ' id="' . self::$id . '">' : '>');
        if ($collection->count() < 1) {
            return false;
        }

        $tableHeadKeys = $collection->getModelKeys();
        if (is_array($fields)) {
            $tableHeadKeys = array_keys($fields);
        }

        //// THEAD /////////
        if (!empty($tableHeadKeys)) {
            $tHead = '<thead>';
            if (!empty(self::$tableName)) {
                $tHead .= '<tr>';
                if ($fields != false) {
                    $colspan = count($fields) + 1;
                } else {
                    $colspan = $collection->count() + 1;
                }

                if (self::$numbering == true) {
                    $colspan++;
                }

                $tHead .= '<th colspan="' . $colspan . '">'
                    . self::$tableName . '</th>';
                $tHead .= '</tr>';
            }
            $tHead .= '<tr>';
            if (is_array($actions) && self::$actionsPosition == 'first') {
                $tHead .= '<th>' . __('actions') . '</th>';
            }
            if (self::$checkbox == true) {
                $mainCheck = '';
                if (self::$mainCheckbox) {
                    $check       = '';
                    $valCheckbox = 0;
                    if (self::$checked) {
                        $check       = 'checked';
                        $valCheckbox = 1;
                    }
                    $mainCheck = $this->view->formCheckbox(
                        'main-checkbox',
                        $valCheckbox,
                        [
                            'class'   => 'main-select',
                            'id'      => 'main-checkbox',
                            'checked' => $check
                        ]
                    );
                }

                $tHead .= '<th>' . __('select') . '<br />' . $mainCheck . '</th>';
            }

            if (self::$numbering == true) {
                $tHead .= '<th>№</th>';
            }

            foreach ($tableHeadKeys as $key) {
                $headParams = $fields[$key];
                $key        = __(str_replace('_id', '', $key));
                $class      = '';
                if (is_array($headParams) && array_key_exists('headerClass', $headParams)) {
                    $class = ' class="' . $headParams['headerClass'] . '"';
                }
                $tHead .= '<th' . $class . '>' . $key . '</th>';
            }

            if (self::$buildAdditionalFields == true) {
                // building additional headers
                $tHead .= $this->_buildAdditionalHeaders($collection, $fields);
            }

            if (is_array($actions) && self::$actionsPosition == 'last') {
                $tHead .= '<th>' . __('actions') . '</th>';
            }

            $tHead .= '</tr></thead>';
            $tableHtml .= $tHead;
        }

        //// TBODY /////////
        $tableHtml .= '<tbody>';
        foreach ($collection as $numer => $item) {
            $id = (self::$key == '' ? $item->getFirstProperty() : $item->getProperty(self::$key));
            empty($id) ? $id = $numer : null;;

            $highlightClass = '';
            if (!empty($highlight)) {
                foreach ($highlight as $field => $values) {
                    foreach ($values as $val => $class) {
                        if ($item->$field == $val) {
                            $highlightClass = 'class="' . $class . '"';
                        }
                    }
                }
            }
            $tableHtml .= '<tr id="row-' . $id . '" ' . $highlightClass . '>';
            if (is_array($actions) && self::$actionsPosition == 'first') {
                $tableHtml .= $this->_buildActions($actions, $item, $view);
            }
            if (self::$numbering == true) {
                $tableHtml .= '<td class="table-row-numbering">' . ($numer + 1) . '</td>';
            }

            if (self::$checkbox == true) {
                $check       = '';
                $valCheckbox = 1;
                if (self::$checked) {
                    $check       = 'checked';
                    $valCheckbox = 1;
                }
                $checkbox = $this->view->formCheckbox(
                    'row[' . $id . ']',
                    $valCheckbox,
                    [
                        'class'   => 'row-select',
                        'id'      => 'check-' . $id,
                        'checked' => $check
                    ]
                );
                $tableHtml .= '<td class="row-checkbox">' . $checkbox . '</td>';
            }

            if (is_array($fields)) {
                foreach ($fields as $key => $val) {
                    $value   = isset($val['key']) ? $item->$val['key'] : false;
                    $tdId    = $key . '-' . $numer;
                    $tdClass = '';
                    $width   = '';
                    if (array_key_exists('class', $val)) {
                        $tdClass = 'class="' . $val['class'] . '"';
                    }
                    if (array_key_exists('width', $val)) {
                        $width = 'width="' . $val['width'] . '"';
                    }
                    $tdCont = '<td ' . $tdClass . ' id="' . $tdId . '" ' . $width . '>%s</td>';

                    if (array_key_exists('select', $val)) {
                        $value = $validator->$val['key']->getMultiOption($value);
                    } else {
                        if (array_key_exists('types', $val)) {
                            $types = Zend_Registry::get('types');
                            $types = array_flip($types[$val['types']]);
                            $value = __($types[(int)$value]);
                        } else {
                            if (array_key_exists('closure_function', $val)) {
                                $func  = $val['closure_function'];
                                $value = $func($item);
                            } else {
                                if (array_key_exists('function', $val)) {
                                    $func  = $val['function'];
                                    $value = $item->$func();
                                } else {
                                    if (array_key_exists('boolean', $val)) {
                                        if (!empty($item->$val['key'])) {
                                            $value = __('yes');
                                        } else {
                                            $value = __('no');
                                        }
                                    } else {
                                        if (array_key_exists('link', $val)) {
                                            if (!is_array($val['link'])) {
                                                $value = '<a class="' . $val['link_class'] . '"
                                    href ="' . $val['link'] . $item->getFirstProperty() . '">'
                                                    . ($item->$val['link_name'] ? $item->$val['link_name'] : $value)
                                                    . '</a>';
                                            } else {
                                                $selfKeyLink = $val['link']['key'];
                                                $href        = '';
                                                if (array_key_exists('href', $val['link'])) {
                                                    $href = $val['link']['href'] . $val['link']['key'] . '/';
                                                }
                                                if (!$item->$selfKeyLink == null) {
                                                    $value = '<a target="_blank" class="' . $val['link_class'] . '"
                                            href ="' . $href . $item->$selfKeyLink . '">'
                                                        . ($item->$val['link_name'] ? $item->$val['link_name'] : $value)
                                                        . '</a>';
                                                }
                                            }
                                        }
                                        if (array_key_exists('href', $val)) {
                                            $href  = '';
                                            $value = '<a target="_blank" class="' . $val['link_class'] . '"
                                            href ="' . $href . $val['href'] . $value . '">'
                                                . ($item->$val['link_name'] ? $item->$val['link_name'] : $value)
                                                . '</a>';
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (array_key_exists('number', $val)) {
                        if ($value > 0) {
                            $value = number_format($value / 100, 2);
                        }
                    }

                    if (array_key_exists('customHelper', $val)) {
                        $customField = $this->_customField($value, $val['customHelper']);
                        $tableHtml .= sprintf($tdCont, $customField);
                        continue;
                    }

                    if (array_key_exists('editable', $val)) {
                        $name  = 'voyage' . $numer . '[' . $key . ']';
                        $input = $this->view->formText($name, $value);
                        $tableHtml .= sprintf($tdCont, $input);
                    } else {
                        if (self::$editable == true) {
                            $error = '';
                            if ($item->getError()) {
                                if (array_key_exists($key, $item->getError())) {
                                    $error  = '<ul class="errors">';
                                    $errors = $item->getError();
                                    foreach ($errors[$key] as $errCode) {
                                        $error .= '<li>' . __($errCode) . '</li>';
                                    }
                                    $error .= '</ul>';
                                }
                            }
                            $name  = 'voyage' . $numer . '[' . $key . ']';
                            $input = $this->view->formText($name, $value);
                            $tableHtml .= sprintf($tdCont, $input . $error);
                        } else {
                            $tableHtml .= sprintf($tdCont, $value);
                        }
                    }
                }
            } else {
                foreach ($item->toArray() as $key => $value) {
                    $tdClass = $key . '-' . $numer;
                    $tdCont  = '<td id="' . $tdClass . '">%s</td>';
                    $tableHtml .= sprintf($tdCont, $value);
                }
            }

            if (self::$buildAdditionalFields == true) {
                $tableHtml .= static::_buildAdditionalFields($collection, $item, $numer);
            }

            if (is_array($actions) && self::$actionsPosition == 'last') {
                $tableHtml .= $this->_buildActions($actions, $item, $view);
            }
            $tableHtml .= '</tr>';
        }
        $tableHtml .= '</tbody></table></div>';

        return $tableHtml;
    }

    protected function _buildAdditionalHeaders($collection, $fields)
    {
    }

    protected static function _buildActions($actions, $item, $view)
    {
        $tableHtml
            = '<td class="table-actions"><div class="button-bar"><ul class="button-group">';
        foreach ($actions as $key => $params) {
            $permission = true;
            if (array_key_exists('restrictions', $params)) {
                foreach ($params['restrictions'] as $fieldKey => $fieldValues) {
                    if ($item->$fieldKey != $fieldValues) {
                        $permission = false;
                        break;
                    }
                }
            }
            if (array_key_exists('function_restrictions', $params)) {
                foreach ($params['function_restrictions'] as $func => $funcValue) {
                    if ($item->$func() != $funcValue) {
                        $permission = false;
                        break;
                    }
                }
            }

            if ($permission == false) {
                continue;
            }
            $reqParam = 'id';
            if (array_key_exists('param', $params)) {
                $reqParam = $params['param'];
            }

            $linkProperty = $item->getFirstProperty();
            if (array_key_exists('modelParam', $params)) {
                $linkProperty = $item->$params['modelParam'];
            }

            $iconId = '';
            if (array_key_exists('id', $params)) {
                $iconId = $params['id'] . $linkProperty;
            }

            $linkParams = '';
            if (array_key_exists('function', $params)) {
                $func       = $params['function'];
                $linkParams = $func($item);
            }

            if (array_key_exists('additional_params', $params)) {
                foreach ($params['additional_params'] as $p) {
                    $linkProperty .= '/';
                    $linkProperty .= $p . '/' . $item->$p;
                }
            }
            if (array_key_exists('func_additional_params', $params)) {
                foreach ($params['func_additional_params'] as $key => $p) {
                    $linkProperty .= '/';
                    $linkProperty .= $key . '/' . $item->$p();
                }
            }

            $button = $view->iconButton(
                $params['link'] . ($linkProperty ? $reqParam . '/' . $linkProperty . '/' : '') . $linkParams,
                $params['icon_class'],
                __($key),
                $iconId,
                $key
            );
            $tableHtml .= $button;
        }
        $tableHtml .= '</ul></div></td>';

        return $tableHtml;
    }

    protected function _customField($value, $viewHelper)
    {
        array_push($viewHelper['args'], $value);
        $viewHelper['args'] = array_reverse($viewHelper['args']);
        $helper             = $viewHelper['helper'];

        return call_user_func_array([$this->view, $helper], $viewHelper['args']);
    }

    protected function _buildAdditionalFields($collection, $item, $numer)
    {
    }
}