<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Alnum.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';
/**
 * @see Zend_Locale
 */
require_once 'Zend/Locale.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Core_Filter_DateConverter implements Zend_Filter_Interface
{
    protected $_fromFormat;

    protected $_toFormat = 'yyyy-MM-dd H:mm';

    public function __construct($fromFormat, $toFormat = null)
    {
        $this->_fromFormat = $fromFormat;
        if (null != $toFormat) {
            $this->_toFormat = $toFormat;
        }
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * @param  string $value
     *
     * @return string
     */
    public function filter($value)
    {
        if (!Zend_Date::isDate($value, $this->_fromFormat)) {
            return $value;
        }

        $date    = new Zend_Date($value, $this->_fromFormat);
        $newDate = $date->toString($this->_toFormat);

        return $newDate;
    }
}
