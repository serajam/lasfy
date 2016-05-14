<?php

/**
 * Appending head links to layouts class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_HeadLinks extends Core_View_Helper_View
{
    public function headLinks($admin = false)
    {
        $admin ? $this->_adminUi() : $this->_userUi();
    }

    protected function _adminUi()
    {
        $host = $this->view->domainLink(1);
        $this->view->headLink()
            ->appendStylesheet($host . 'foundation/normalize.css')
            ->appendStylesheet($host . 'css/basic.css')
            ->appendStylesheet($host . 'foundation/foundation.min.css')
            ->appendStylesheet($host . 'js/plugins/responsive-tables/responsive-tables.css')
            ->appendStylesheet($host . 'foundation/icons/foundation-icons.css')
            ->appendStylesheet($host . 'js/plugins/chosen_v1.1.0/chosen.min.css')
            ->appendStylesheet($host . 'css/lightbox.css');
        $this->view->headScript()
            ->prependFile($host . 'js/jquery-1.11.3.min.js')
            ->prependFile($host . 'foundation/modernizr.js')
            ->appendFile($host . 'foundation/foundation.min.js')
            ->appendFile($host . 'js/tinymce/tinymce.min.js')
            ->appendFile($host . 'js/plugins/ajaxModalUrl.js')
            ->appendFile($host . 'js/plugins/ajax.modalViewer.js')
            ->appendFile($host . 'js/plugins/ajax.modalForm.submitter.js')
            ->appendFile($host . 'js/plugins/ajax.url.requester.js')
            ->appendFile($host . 'js/plugins/ajax.form.submitter.js')
            ->appendFile($host . 'js/plugins/modalMessage.js')
            ->appendFile($host . 'js/plugins/responsive-tables/responsive-tables.js')
            ->appendFile($host . 'js/client/lightbox.js')
            ->appendFile($host . 'js/jquery.form.js')
            ->appendFile($host . 'js/basicAdmin.js')
            ->appendFile($host . 'js/plugins/chosen_v1.1.0/chosen.jquery.min.js')
            ->appendFile($host . 'js/jquery.countdown.js');
    }

    protected function _userUi()
    {
        $host = $this->view->domainLink(1);
        $uri  = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        // $this->view->headLink(['rel' => 'canonical', 'href' => $host . substr_replace($uri, '', 0, 1),], 'APPEND');

        $options = Zend_Registry::get('appConfig');
        $options['useMinify'] ? $cache = '&' . $options['staticRevision'] : $cache = '';

        if (APPLICATION_ENV == Core_App::DEVELOPMENT) {
            $this->view->headLink()
                ->appendStylesheet($host . 'foundation/normalize.css')
                ->appendStylesheet($host . 'foundation/foundation.min.css')
                ->appendStylesheet($host . 'foundation/icons/foundation-icons.css')
                ->appendStylesheet($host . 'css/jquery.tag-editor.css')
                ->appendStylesheet($host . 'css/style.css')
                ->appendStylesheet($host . 'css/jquery.mCustomScrollbar.css')// ->appendStylesheet('css/lightbox.css')
            ;

            $this->view->headScript()
                ->appendFile($host . 'js/jquery-1.11.3.min.js')
                //->appendFile('js/jquery-ui.min.js')
                ->appendFile($host . 'foundation/modernizr.js')
                ->appendFile($host . 'foundation/foundation.min.js')
                ->appendFile($host . 'js/plugins/tagEditor/jquery.caret.js')
                ->appendFile($host . 'js/plugins/tagEditor/jquery.tag-editor.js')
                ->appendFile($host . 'js/client/jquery.ez-bg-resize.js')
                ->appendFile($host . 'js/client/jquery.orbit-1.2.3.min.js')
                ->appendFile($host . 'js/client/jquery.mCustomScrollbar.min.js')
                ->appendFile($host . 'js/client/jquery.mousewheel.min.js')
                ->appendFile($host . 'js/plugins/ajaxModalUrl.js')
                ->appendFile($host . 'js/plugins/ajax.modalForm.submitter.js')
                ->appendFile($host . 'js/plugins/ajax.form.submitter.js')
                ->appendFile($host . 'js/plugins/ajax.url.requester.js')
                ->appendFile($host . 'js/plugins/modalMessage.js')
                ->appendFile($host . 'js/plugins/tagsSearchWorker.js')
                ->appendFile($host . 'js/plugins/opft.js')
                ->appendFile($host . 'js/jquery.form.js')
                ->appendFile($host . 'js/mustache.min.js')
                // ->appendFile('js/client/lightbox.js')
                ->appendFile($host . 'js/client/basic.js')
                ->appendFile($host . 'js/jquery.countdown.js');
        } elseif (APPLICATION_ENV == Core_App::PRODUCTION) {
            $this->view->headLink()->appendStylesheet($host . 'css/min_styles.css?v1');
            $this->view->headScript()->appendFile($host . 'js/client/scripts.min.js?v1');
        }

        $this->view->headScript()->appendFile($host . 'js/jquery-ui.min.js');
    }
}