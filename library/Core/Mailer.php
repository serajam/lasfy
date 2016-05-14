<?php

// TODO Rewright to english comments

/**
 * Класс для отправки писем из системы
 * реализующий возможность конвертации писем в различные
 * кодировки а также составление писем из шаблонов в базе данных.
 * Класс не является универсальным так как пока не позволяет переопределить отправителя и т.д.
 *
 */
class Core_Mailer extends Zend_Mail
{
    /**
     * Кодировка отправляемых писем
     *
     * @var string
     */
    protected $_charset = 'utf-8';

    /**
     * Хранилище переменных для замены
     *
     * @var array
     */
    private $_variables = [];

    /**
     * Констурктор. Принимает в качестве параметров
     * имя письма в базе данных а также масив переменных для замены.
     *
     * @param string $mailName
     * @param array  $vars
     */
    public function __construct($mailName = null, $vars = null, $type = 0, $images = false)
    {
        if (!empty ($mailName) && !empty ($vars)) {
            $this->_variables = $vars;

            $defaultLang = Zend_Registry::get('language');
            $letter      = Core_DbTable_Mail::getMail($mailName, $defaultLang);
            if (empty ($letter)) {
                $letter = Core_DbTable_Mail::getMail($mailName);
            }
            if (empty ($letter)) {
                error_log('No message template: ' . $mailName);

                return false;
            }

            if (!$type) {
                $this->setBodyText($this->_replace($letter ['mailBody']));
            } elseif ($type) {
                $this->addHeader('Content-Language', $defaultLang);
                $this->addHeader('Content-Type', 'multipart/mixed', true);
                $this->setBodyHtml(
                    $this->_replace($letter ['mailBody']),
                    'utf-8',
                    Zend_Mime::ENCODING_QUOTEDPRINTABLE,
                    $images
                );
            }
            $this->setSubject($this->_replace($letter ['mailSubject']));
        }
        $config = new Zend_Config_Ini (
            APPLICATION_PATH . '/config/application.ini', 'development'
        );

        $this->setMessageId();
        $this->setFrom($config->service_email, __('siteName'));
        $this->addHeader('MIME-Version', '1.0');
        $this->addHeader('Content-Transfer-Encoding', '8bit');
        $this->addHeader('X-Mailer:', 'PHP/' . phpversion());
        $this->addHeader('X-Sender:', $config->service_email);
        $this->setReplyTo($config->service_email, 'Service Email');
    }

    /**
     * Заменяет все переменные найденные в тексте
     * $replacment на их значения
     *
     * @param string $replacment
     *
     * @return string
     */
    private function _replace($text)
    {
        foreach ($this->_variables as $var => $replacment) {
            $text = preg_replace('/{{' . $var . '}}/', $replacment, $text);
        }

        return $text;
    }



    public function setBodyHtml(
        $txt,
        $charset = null,
        $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE,
        $images = false
    )
    {
        if ($images) {
            $config       = Zend_Registry::get('appConfig');
            $baseHttpPath = $config['baseHttpPath'];
            $this->setType(Zend_Mime::MULTIPART_RELATED);
            $matches = [];
            preg_match_all(
                "#<img.*?src=['\"]([^'\"]+)#i",
                $txt,
                $matches
            );
            $matches = array_unique($matches[1]);
            if (count($matches) > 0) {
                foreach ($matches as $key => $filename) {
                    $filePath = APPLICATION_PATH . '/../public_html' . $filename;
                    if (is_readable($filePath)) {

                        $at              = $this->createAttachment(file_get_contents($filePath));
                        $at->filename    = 'logo';
                        $at->type        = $this->mimeByExtension($filePath);
                        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                        $at->encoding    = Zend_Mime::ENCODING_BASE64;
                        $at->id          = 'cid_' . md5_file($filePath);
                        $txt             = str_replace($filename, 'cid:' . $at->id, $txt);
                    }
                }
            }
        }
        parent::setBodyHtml($this->_convert($txt), $charset, $encoding);
    }

    public function mimeByExtension($filename)
    {
        if (is_readable($filename)) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'gif':
                    $type = 'image/gif';
                    break;
                case 'jpg':
                case 'jpeg':
                    $type = 'image/jpg';
                    break;
                case 'png':
                    $type = 'image/png';
                    break;
                default:
                    $type = 'application/octet-stream';
            }
        }

        return $type;
    }

    /**
     * Текст письма. Создано для возможности отправки писем без базы данных.
     *
     * @param string
     */
    /*	public function setBodyText($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
     {
         $txt = $this->_convert ( $txt );
         parent::setBodyText ( $txt, $charset, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE );
     }*/
    /**
     * Конвертирует строку в кодировку windows-1251
     * так как не все почтовые клиенты понимают utf-8
     *
     * @param string $txt
     *
     * @return string
     */
    private function _convert($txt)
    {
        return iconv('utf-8', $this->_charset, $txt);
    }

    /**
     * Определяет тему письма. Создано для возможности отправки писем без базы данных.
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        parent::setSubject($this->_convert($subject));
    }

    /**
     * Определяет отправителя
     *
     * @param string $email
     * @param string $name
     */
    public function setFrom($email, $name = null)
    {
        if (!empty ($name)) {
            $name = $this->_convert($name);
        }
        parent::setFrom($email, $name);
    }

    public function send($tr = null)
    {
        parent::send($tr);
    }

    /**
     * Определяет получателя
     *
     * @param string $email
     * @param string $name
     */
    public function addTo($email, $name = '')
    {
        if (!empty ($name)) {
            $name = $this->_convert($name);
        }
        parent::addTo($email, $name);
    }
}

?>