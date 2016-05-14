<?php

/**
 * Created by PhpStorm.
 * User: alexius
 * Date: 23.01.15
 * Time: 14:40
 */
class Core_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $languages = Zend_Registry::get('languages');
        $config    = Zend_Registry::get('appConfig');
        $lang      = $request->getParam('lang', $config['lang']);

        $module = $request->getModuleName();

        if ((empty($lang) || !isset($languages['languages'][$lang])) && $module != 'admin') {
            $this->getResponse()->setRedirect($config['baseHttpPath'] . $lang . '/');
        }

        if (!isset($languages['languages'][$lang])) {
            $lang = $config['lang'];
        }

        Zend_Registry::set('language', $lang);
        $translation = new Core_Translation();
        $translation->init(null, $lang);
        Zend_Registry::set('translation', $translation);

        $locale = new Zend_Locale($config['locale'][$lang]);
        Zend_Registry::set('Zend_Locale', $locale);

        $translator = new Zend_Translate(
            'array',
            APPLICATION_PATH . '/data/languages/',
            $locale->getLanguage(),
            ['scan' => Zend_Translate::LOCALE_DIRECTORY]
        );
        $translator->addTranslation(['content' => $translation->getTranslation(), 'locale' => $lang]);
        Zend_Registry::set('Zend_Translate', $translator);
        Zend_Validate_Abstract::setDefaultTranslator($translator);

        // Set language to global param so that our language route can
        // fetch it nicely.
        $front  = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->setGlobalParam('lang', $lang);
    }
}