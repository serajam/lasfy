<?php

/**
 * Appending head links to layouts class
 *
 * @author     Fedor Petryk
 * @copyright
 */
class Core_View_Helper_Html_SiteMenu extends Core_View_Helper_View
{
    const MENU_ITEM_TEMPLATE = '<li><a class="%s" href="%s">%s</a></li>';
    const MENU_ITEM_DROP_DOWN_TEMPLATE = '<li class="has-dropdown"><a href="%s">%s</a><ul class="dropdown">%s</ul></li>';
    const MENU_ACTIVE_CLASS = 'active';

    protected $pageTypes = [1, 5, 2, 4];  // page types which are displayed as menu with custom url

    public function siteMenu()
    {
        $params    = [];
        $uri       = '';
        $contentId = 0; // id of the content which is bound to menu item
        $service   = $this->view->service;
        $menuArr   = $service->getSiteMenu();
        $this->clearMenu($menuArr);
        $html = '';

        if (!count($menuArr)) {
            return false;
        }

        if (!$this->view->page) {
            $controller = Zend_Controller_Front::getInstance();
            $params     = $controller->getRequest()->getParams();
            $uri        = $controller->getRequest()->getRequestUri();
        } // in case menu is a simple page with content
        else {
            $contentId = $this->view->page->pageId;
        }

        $html .= $this->buildMenuTree($menuArr, $contentId, $params, $uri);
        $html .= $this->addMessagesItem();
        $html .= $this->addLanguagesItem();

        return $html;
    }

    /**
     * Clear menu by deleting not permitted items
     *
     * @param $menu
     *
     * @return mixed
     */
    public function clearMenu(&$menu)
    {
        $auth = Zend_Auth::getInstance();
        foreach ($menu as $key => $item) {
            $page = $item['page'];
            if ($page['availability'] == 2 && !$auth->hasIdentity()) // if user unauthorized
            {
                unset($menu[$key]);
                continue;
            }

            if ($page['availability'] == 3 && $auth->hasIdentity()) {
                unset($menu[$key]);
                continue;
            }

            if (isset($page['subPage'])) {
                $this->clearMenu($page['subPage']);
            }
        }
    }

    protected function buildMenuTree($menuArr, $contentId, $params, $uri)
    {
        $html = '';
        foreach ($menuArr as $key => $menu) {
            $page = $menu['page'];

            // the first element is addAd menu item - make it red
            $page['routeCode'] == 'add-ad' ? $active = 'addAd visible-for-medium-up ' : $active = '';

            $active .= $this->checkItemIsActive($page, $contentId, $params, $uri);
            try {
                $link = $this->buildLink($page);
            } catch (Exception $e) {
                error_log(__METHOD__ . '. Unable to build route for menu item' . $page['menuId']);
                continue;
            }

            if (!isset($menu['subPage'])) {
                $html .= sprintf(self::MENU_ITEM_TEMPLATE, $active, $link, __($page['name']));
            } else {
                $html .= sprintf(
                    self::MENU_ITEM_DROP_DOWN_TEMPLATE,
                    $link,
                    __($page['name']),
                    $this->buildMenuTree($menu['subPage'], $contentId, $params, $uri)
                );
            }
        }

        return $html;
    }

    /**
     * Check if current menu item is active
     *
     * @param array  $menu
     * @param int    $contentId
     * @param array  $params
     * @param string $uri
     *
     * @return string
     *
     * @author Fedor Petryk
     */
    protected function checkItemIsActive($menu, $contentId, $params, $uri)
    {
        $active = '';
        if ($menu['contentId'] === $contentId) {
            $active .= self::MENU_ACTIVE_CLASS;
        } elseif (!empty($params) && mb_strstr($menu['link'], $params['action'])) {
            $active .= self::MENU_ACTIVE_CLASS;
        } elseif (isset($uri) && strstr($uri, $menu['link'])) {
            $active .= self::MENU_ACTIVE_CLASS;
        }

        return $active;
    }

    /**
     * Build menu item link depending on input params
     *
     * @param array $menu
     *
     * @return string
     *
     * @author Fedor Petryk
     */
    protected function buildLink($menu)
    {
        $link = '';
        if (in_array($menu['type'], $this->pageTypes)) {
            if (empty($menu['slug'])) {
                $baseLink = $this->view->domainLink(1, true);
                $link     = $baseLink . 'default/page/index/id/' . $menu['contentId'];
            } else {
                $link = $this->view->url([1 => $menu['slug']], $menu['routeCode']);
            }
        } elseif ($menu['type'] == 3) {
            $link = $this->view->url([], $menu['routeCode']);
        }

        return $link;
    }

    /**
     * Messages menu item
     *
     * @return string
     *
     * @author Fedor Petryk
     */
    protected function addMessagesItem()
    {
        if (!Zend_Auth::getInstance()->hasIdentity() || !Core_Model_User::getInstance()->roleName) {
            return '';
        }

        $totalMessages = MessagesAccess::countNewMessages();
        $messagesLink  = $this->view->url([], 'conversations');
        $icon          = $this->view->iconButton(
            $messagesLink,
            'mail pl10',
            __('messages'),
            '',
            'fs20',
            '',
            $totalMessages ? '<span class="fs14 messagesCount">(' . $totalMessages . ')</span>' : ''
        );

        return '<li>' . $icon . '</li>';
    }

    /**
     * Change language menu item
     *
     * @return string
     * @throws Zend_Exception
     *
     * @author Fedor Petryk
     */
    protected function addLanguagesItem()
    {
        $conf      = Zend_Registry::get('languages');
        $languages = array_keys($conf['languages']);

        if (!is_array($languages)) {
            return '';
        }

        $defaultLang = Zend_Registry::get('language');

        $aLang = '<select class="langSelect">';
        foreach ($languages as $lang) {
            $selected = '';
            if (!strcmp($lang, $defaultLang)) {
                $selected = 'selected';
            }
            $aLang .= '<option value="' . $lang . '"' . $selected . '>' . $lang . '</option>';
        }
        $aLang .= '</select>';

        return '<li>' . $aLang . '</li>';
    }
}