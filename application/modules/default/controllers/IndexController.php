<?php

/**
 * User authentication controller
 *
 * @author      Fedor Petryk
 * @copyright   Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Default_IndexController extends Core_Controller_Start
{
    protected $_defaultServiceName = 'DefaultService';

    /**
     * @var DefaultService
     */
    protected $_service;

    protected $_pagination = true;

    /**
     * @var Job_Ads_Service
     */
    protected $_adsService;

    protected $_userId;

    public function init()
    {
        //$this->_helper->layout->setLayout('profile');
        $this->_adsService = new Job_Ads_Service();
        parent::init();
    }

    public function preDispatch()
    {
        $this->view->isLight = true;
        $auth                = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $slugs = $this->_service->getDefaultMenu();

            $layout        = $this->_helper->layout->getLayoutInstance();
            $layout->slugs = $slugs;
        }
        $this->_userId = $this->view->userId = Core_Model_User::getInstance()->userId;
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('profile');
        $pageSlug = $this->_request->getParam('slug', 0);
        $lang     = Zend_Registry::get('language');

        if ($pageSlug) {
            $page             = $this->_service->getMapper()->getPage($pageSlug, false, $lang);
            $this->view->page = $page;

            return true;
        } else {
            if ($tag = $this->_request->getParam('tag', null)) {
                $page = $tag ? $this->_service->getMapper()->getPage($tag, false, $lang) : false;
            } else {
                $page = $this->_service->getMapper()->getPage($lang . '/', false, $lang);
            }

            if (!empty($page)) {
                $this->view->page = $page;
                $page->metaTitle ? $this->view->title = $page->metaTitle : false;
                $page->metaKeywords ? $this->view->keywords = $page->metaKeywords : false;
                $page->metaDescription ? $this->view->description = $page->metaDescription : false;
            }

            $page  = $this->_getParam('page', $this->_pagination);
            $param = $this->_getParam('param', null);
            if (!strcasecmp($param, 'showAll')) {
                $this->view->vacancies = $this->_adsService->getVacanciesForPeriod(false, false, $page);
                $this->view->resumes   = $this->_adsService->getResumeForPeriod(false, false, $page);
                $this->view->showAll   = $param;
                $this->view->userId    = $this->_userId;
                $this->view->page      = null;
            } elseif ($tag) {
                $tags = $this->_service->getMapper()->getExistedTagsByName([$tag]);

                if (!$tags || !count($tags)) {
                    $this->noPage();

                    return false;
                }

                $this->view->tags    = $tags;
                $this->view->showAll = 1;
                $this->view->userId  = $this->_userId;
            }
        }
    }

    public function contactsAction()
    {
        $this->view->form   = $this->_service->getFeedbackForm();
        $this->view->header = __('feedbackForm');
        $this->view->email  = $this->_service->getConfigParam('infoEmail');
        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            $this->_service->sendEmail($data);
        }

        $this->setPageMetaData();
    }

    public function signInAction()
    {
        $this->_helper->layout->setLayout('simple');
        if ($this->_helper->FlashMessenger->hasMessages()) {
            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
            $this->_helper->FlashMessenger->clearCurrentMessages();
        }

        $this->view->header = __('signInUp');

        $requested             = $this->_request->getParam('redirect');
        $this->view->requested = $requested;

        $post = $this->_request->getParams();
        if ($this->_request->isPost() && !isset($post['provider'])) {

            $resAuth = isset($post['signIn'])
                ? $this->_service->signIn($post)
                : $this->_service->signUp($post);

            if (isset($resAuth['url']) && !isset($requested)) {
                $this->_redirect($resAuth['url']);
            }
            if ($requested) {
                $this->_redirect($requested);
            }
        }

        if (isset($post['provider'])) {
            $provider = $post['provider'];
            try {
                $socialConf = APPLICATION_PATH . '/../library/hybridauth/config.php';

                //require_once(APPLICATION_PATH . '/../library/hybridauth/config.php');
                $socialConf             = include $socialConf;
                $socialConf["base_url"] = $this->view->domainLink(
                        1,
                        true
                    ) . 'hybridauth'; // . $this->view->url([], 'hybridauth');

                //http://uo.com.ua/default/index/hybridauth-endpoint
                // initialize Hybrid_Auth with a given file
                $hybridauth = new Hybrid_Auth($socialConf);

                // try to authenticate with the selected provider
                $adapter = $hybridauth->authenticate($provider);

                // then grab the user profile
                $userProfile = $adapter->getUserProfile();
            } catch (Exception $e) {
                echo "Error: please try again!";
                echo "Original error message: " . $e->getMessage();
            }

            if (isset($_GET['userEmail'])) {
                $userProfile->email = $_GET['userEmail'];
            }

            $res = $this->_service->thirdPartRegistration($userProfile, $provider);
            if (!$res) {
                $fMessages = multi_implode(',', $this->_service->getFormMessages());
                $this->_helper->flashMessenger->addMessage(
                    $this->_service->getError() . ': ' . $fMessages
                );
            }
        }
        $this->view->signInForm = $this->_service->getSignInForm();
        $this->view->signUpForm = $this->_service->getSignUpForm();

        $this->setPageMetaData();
    }

    public function hybridauthEndpointAction()
    {
        include_once APPLICATION_PATH . '/../library/hybridauth/index.php';
    }

    public function activationAction()
    {
        $code = $this->getParam('code', 0);

        $this->_service->activateUser($code);
    }

    public function forgotPasswordAction()
    {
        $this->view->header = __('recoveryPassword');
        $this->view->form   = $this->_service->getRecoveryPasswordForm();

        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            $this->_service->recoveryPassword($data);
        }

        $this->setPageMetaData();
    }

    public function passwordActivationAction()
    {
        $this->view->header = __('successfulActivation');
        $code               = $this->_request->getParam('code');

        if (empty($code)) {
            $this->_redirect('/');
        }

        if ($this->_service->activatePassword($code)) {
            $this->view->activated = true;
        }
    }

    public function addAdAction()
    {
        $this->view->header      = __('add_resume_or_vacancy');
        $this->view->vacancyForm = $this->_adsService->getVacancyForm();
        $this->view->resumeForm  = $this->_adsService->getResumeForm();
        $this->view->initialTags = $this->_adsService->getInitialTags();

        if ($this->_request->isPost()) {
            $post  = $this->_request->getParams();
            $addAd = $post['process'];
            $this->_adsService->editAd($post, $this->view->id, $addAd);
            $this->_adsService->getMessage()
                ? $this->_service->setMessage($this->_adsService->getMessage())
                :
                $this->_service->setError($this->_adsService->getError());
            //$this->_service->setMessage($this->_adsService->getMessage());
        }

        $this->setPageMetaData();
    }
}