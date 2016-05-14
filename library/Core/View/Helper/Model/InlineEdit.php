<?php

/**
 *
 * Messenger helper class class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Model_InlineEdit extends Core_View_Helper_View
{
    /**
     * @var Core_Form
     */
    public function inlineEdit($options, $link)
    {
        if (empty($options)) {
            return false;
        }

        $hmtl
            = '<table class="default-table">
			<thead>
				<tr>
					<th colspan=3>' . $this->view->translation('company_settings') . '</th>
				</tr>
			</thead>
			<tbody>
			%s
			</tbody>
			</table>';

        $tbody = '';
        foreach ($options as $field => $value) {
            $tbody
                .= '<tr>
					<td></td>
					<td id="' . $field . '"></td>
					<td class="comment-edit"></td>
				</tr>';
        }

        return sprintf($hmtl, $tbody);
    }
}

?>