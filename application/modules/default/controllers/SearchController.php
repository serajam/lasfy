<?php

/**
 * Search engine controller.
 *
 * @author      Alexey Kagarlykskiy
 * @copyright   Copyright (c) 2013-2015 Studio 105 (http://105.in.ua)
 */
class Default_SearchController extends Core_Controller_Start
{
    protected $_defaultServiceName = 'SearchService';

    /**
     * @var SearchService
     */
    protected $_service;

    protected $_pagination = true;

    protected $_userId;

    public function preDispatch()
    {
        $this->_userId = Core_Model_User::getInstance()->userId;
    }

    public function getAdsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $post = $this->_request->getPost();
            $page = $this->_getParam('page', $this->_pagination);
            $type = $this->getParam('type', false);

            $separateSearch = isset($post['separateSearch']) ? $post['separateSearch'] : $post['separateSearchSelect'];

            if (!empty($post['searchTags'])) {
                $ads = $this->_service->getAdsByTags($post, $page, $separateSearch, $type);
            } else {
                $ads = $this->_service->getAdsLastMonth($post, $page, $separateSearch, $type);
            }

            $ads['resumesPagination']   = !isset($ads['resumesPaginator'])
                ?: $this->renderPagination(
                    $ads['resumesPaginator'],
                    'Resumes'
                );
            $ads['jooblePagination']    = !isset($ads['vacanciesJooblePaginator'])
                ?: $this->renderPagination(
                    $ads['vacanciesJooblePaginator'],
                    'PartnersVacancies'
                );
            $ads['vacanciesPagination'] = !isset($ads['vacanciesPaginator'])
                ?: $this->renderPagination(
                    $ads['vacanciesPaginator'],
                    'Vacancies'
                );

            unset($ads['resumePaginator'], $ads['jooblePaginator'], $ads['vacanciesPaginator']);
            !$type ?: $ads['paging'] = 1;

            $this->_service->setJsonData($ads);

            $this->view->userId = $this->_userId;
        } else {
            $types = ['vacancy', 'resume'];
            $type  = $this->_getParam('type', false);
            if ($type && in_array($type, $types)) {
                $adId = $this->_getParam("{$type}Id", false);
                if (!empty($adId)) {
                    $ad = $this->_service->getAdByTypeAndId($type, $adId);
                    if (empty($ad)) {
                        $this->_redirect($this->_lang . '/no-page');
                    }
                    $this->view->ad     = $ad;
                    $this->view->userId = $this->_userId;
                    $this->render($type);
                } else {
                    $this->_redirect($this->_lang . '/no-page');
                }
            } else {
                $this->_redirect($this->_lang . '/no-page');
            }
        }
    }

    protected function renderPagination($paginator, $type)
    {
        if (empty($paginator)) {
            return '';
        }
        $paginatorHelper = $this->view->getHelper('PaginationControl');

        return $paginatorHelper->paginationControl(
            $paginator,
            'Sliding',
            "_pagination{$type}.phtml"
        );
    }

    public function getTagsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && !$this->_request->isPost()) {
            $tag = $this->getParam('tag');
            if (isset($tag)) {
                $this->_service = new Job_Tags_TagsService();
                $this->_service->getTags($tag);
            }
        }
    }
}