<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 29.08.13
 * Time: 20:37
 * To change this template use File | Settings | File Templates.
 */
class Admin_GalleryController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'GalleryService';

    /**
     * The service layer object
     *
     * @var GalleryService
     */
    protected $_service;

    protected $_htmlContainer = 'gallery';

    public function deleteImagesAction()
    {
        $this->_service->deleteImages((int)$this->getParam('image'), (int)$this->getParam('gallery'));
    }

    public function addImagesAction()
    {
        $images = $this->getParam('images');
        if (empty($images)) {
            $type                                  = $this->getParam('type');
            $images[(int)$this->getParam('image')] = $type;
        }
        $galleryId = (int)$this->getParam('gallery');
        $this->_service->addImages($galleryId, $images);
    }

    public function galleryListAction()
    {
    }

    public function editGalleryAction()
    {
        $gid = (int)$this->getParam('id');
        if ($gid) {
            if ($this->getRequest()->isXmlHttpRequest() && !empty($this->getParam('data'))) {
                $data = $this->getParam('data');
                $this->_service->sortGalleryImages($gid, $data);
            }

            $this->view->images  = $this->_service->getGalleryImages($gid);
            $this->view->gallery = $this->_service->getGallery($gid);
        }
        if ($this->_request->isPost()) {
            $this->_service->editGallery($this->_request->getPost(), $this->view->id);
        }
    }

    public function deleteGalleryAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_service->deleteGallery((int)$this->getParam('id'));
        }
    }

    public function uploadAction()
    {
        if ($this->_request->isPost()) {
            $this->_service->uploadImage($this->_request->getPost());
        }
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->serviceResponse();
    }

    public function deleteUploadAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_service->uploadDelete($this->getParam('image'));
        }
    }

    public function indexAction()
    {
        $params = false;
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
        }
        $this->view->imagesContainers = $this->_service->getImageCollection($params);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_service->filterPhotos($this->_request->getParams());
        }
    }

    public function sortImagesAction()
    {
    }
}