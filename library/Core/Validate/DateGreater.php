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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Date.php 22668 2010-07-25 14:50:46Z thomas $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Core_Validate_DateGreater extends Zend_Validate_Abstract
{
    const NOT_GREATER = 'dateInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates
        = [
            self::NOT_GREATER => "'%value%' not greater than '%dates%'",
        ];

    /**
     * @var array
     */
    protected $_messageVariables
        = [
            'dates' => '_date'
        ];

    /**
     * Optional format
     *
     * @var string|null
     */
    protected $_date;

    /**
     * Optional locale
     *
     * @var string|Zend_Locale|null
     */
    protected $_locale;

    /**
     * Sets validator options
     *
     * @param  string|Zend_Config $options OPTIONAL
     *
     * @return void
     */
    public function __construct($date)
    {
        $this->setDate($date);

        $this->_messageTemplates = [
            self::NOT_GREATER =>
                Zend_Registry::get('translation')->get('date_greater_than')
        ];
    }

    /**
     * Returns the locale option
     *
     * @return string|Zend_Locale|null
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Sets the locale option
     *
     * @param  string|Zend_Locale $locale
     *
     * @return Zend_Validate_Date provides a fluent interface
     */
    public function setLocale($locale = null)
    {
        require_once 'Zend/Locale.php';
        $this->_locale = Zend_Locale::findLocale($locale);

        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string|null
     */
    public function getDate()
    {
        return $this->_format;
    }

    /**
     * Sets the format option
     *
     * @param  string $format
     *
     * @return Zend_Validate_Date provides a fluent interface
     */
    public function setDate($format = null)
    {
        $this->_date = $format;

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value is a valid date of the format YYYY-MM-DD
     * If optional $format or $locale is set the date format is checked
     * according to Zend_Date, see Zend_Date::isDate()
     *
     * @param  string|array|Zend_Date $value
     *
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        $fieldDate   = new Zend_Date($value, 'yyyy-MM-dd H:m');
        $compareDate = new Zend_Date($this->_date, 'yyyy-MM-dd H:m');

        $dateCompare = $fieldDate->compare($compareDate);
        if ($dateCompare == -1 || $dateCompare == 0) {
            $this->_error(self::NOT_GREATER);

            return false;
        }

        return true;
    }
}
