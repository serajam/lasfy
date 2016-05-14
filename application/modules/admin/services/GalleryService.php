<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 29.08.13
 * Time: 20:38
 * To change this template use File | Settings | File Templates.
 */
class GalleryService extends Core_Service_Editor
{
    /**
     * @var GalleryMapper
     */
    protected $_mapper = null;

    /**
     * Mapper class name
     *
     * @var string
     */
    protected $_mapperName = 'GalleryMapper';

    protected $_validatorName = 'ImageUploadForm';

    public function __construct()
    {

        parent::__construct();
    }

    public function deleteImages($imageId, $galleryId)
    {
        if (!$imageId || !$galleryId) {
            $this->setError(__('error'));

            return false;
        }
        $this->_mapper->deleteGalleryImage($imageId, $galleryId);
        $this->setMessage(Core_Messages_Message::getMessage(1));
    }

    public function addImages($galleryId, $images)
    {
        if (empty($images)) {
            $this->setError(__('no_images_selected'));

            return false;
        }
        if (!is_array($images)) {
            $this->setError(__('no_images_selected'));

            return false;
        }
        $res = $this->_mapper->addGalleryImages($galleryId, $images);
        if ($res !== true) {
            $this->setError(__('image_exists'));

            return false;
        }
        $this->setMessage(__('added'));
    }

    public function uploadDelete($id)
    {
        $this->_mapper->setRowClass('Image');
        $this->_mapper->setCollectionClass('ImageCollection');
        $images = $this->_mapper->fetchAllWhere('`imageId` = "' . $id . '"');
        if (!empty($images)) {
            foreach ($images as $i) {
                $path = BASE_PATH . '/uploads/' . $i->imageName;
                if (is_file($path)) {
                    unlink($path);
                }
            }
            $this->_mapper->deleteImage($id);
        }

        $this->setMessage(Core_Messages_Message::getMessage(1));

        return true;
    }

    /**
     * @param $post
     *
     * @return bool
     */
    public function uploadImage($post)
    {
        $validator = $this->getUploadForm();
        if (!$validator->isValid($post)) {
            $this->_processFormError($validator);

            return false;
        }
        $values  = $validator->getValues();
        $adapter = $validator->file->getTransferAdapter();
        $adapter->setOptions(['ignoreNoFile' => true], $adapter->getFileInfo());
        if (!$adapter->isReceived()) {
            $this->setError(__('no_file'));

            return false;
        }
        $imageHandler = new Core_Image_Manager();
        foreach ($adapter->getFileInfo() as $info) {
            if ($info['tmp_name'] == '') {
                continue;
            }
            $imageHandler->addStack($info);
            list(, , $type) = getimagesize($info['tmp_name']); //type  1 = GIF, 2 = JPG, 3 = PNG
            if ($type != 2) {
                $imageHandler->setConvertType('jpg');
            }
        }
        if (array_key_exists('compression', $values)) { // check and resize
            $compressionSettings = Core_Settings_Settings::getGroupSettings('gallery');
            $sizes               = [];
            foreach ($values['compression'] as $compress) {
                $sizes[$compress] = $compressionSettings[$compress];
            }
            $imageHandler->setSizes($sizes);
        }
        $collection = $imageHandler->runProcess();
        if ($collection->count()) {
            $status = $this->_mapper->insertImages($collection, $values);
            if ($status) {
                $this->setMessage(Core_Messages_Message::getMessage(1));

                return true;
            }
        }
    }

    /**
     * Init and return form to upload image
     *
     * @return Core_Form|null
     */
    public function getUploadForm()
    {
        $settings = Core_Settings_Settings::getGroupSettings('gallery'); //compression gallery param
        //$categorySettings = Core_Settings_Settings::getGroupSettings('gallery_category');
        $form = $this->_validator;
        if ($settings) {
            $form->getElement('compression')->setMultiOptions($settings);
            //  $form->getElement('compression')->setValue('size_1');
        }

        /* if ($categorySettings) {
             $form->getElement('category')->setMultiOptions($categorySettings);
         }*/

        return $form;
    }

    /**
     * @return image collection
     */

    public function getImageCollection($params)
    {
        $word = $params['name'];

        return $this->_mapper->getImages(false, $word);
    }

    public function getChildGalleryForm($galleryId)
    {
        if ($form = $this->getFormLoader()->isExists('ChildGalleryForm')) {
            return $form;
        }
        $form = $this->getFormLoader()->getForm('ChildGalleryForm');
        $form->getElement('parentId')->setValue($galleryId);

        return $form;
    }

    public function deleteGallery($galleryId)
    {
        $db = $this->_mapper->getAdapter();
        $db->delete('Galleries', ['galleryId = ?' => $galleryId]);
        $this->setMessage(__(1));

        return true;
    }

    public function editGallery($post, $id = null)
    {
        $this->_mapper->setDbTable('Core_Image_Table_Galleries')->setRowClass('Core_Image_Gallery');
        $this->setValidatorForm($this->getGalleryForm());
        parent::edit($post, $id);
    }

    public function getGalleryForm($gallery = false)
    {
        if ($form = $this->getFormLoader()->isExists('GalleryForm')) {
            return $form;
        }
        $form = $this->getFormLoader()->getForm('GalleryForm');
        /* $rootGalleries = $this->_mapper->getRootGalleryPairs();
         $form->getElement('parentId')->setMultioptions(array('' => __('select')) + $rootGalleries);*/

        $settings = Core_Settings_Settings::getGroupSettings('gallery'); //compression gallery param
        $form->getElement('thumbnailSize')->setMultiOptions(['' => __('select')] + $settings);
        $form->getElement('fullSize')->setMultiOptions(['' => __('select')] + $settings);

        if ($gallery) {
            $form->populate($gallery->toArray());
        }

        return $form;
    }

    public function getGalleries()
    {
        $this->_mapper->setCollectionClass('Core_Image_Collection_Galleries')
            ->setDbTable('Core_Image_Table_Galleries')->setRowClass('Core_Image_Gallery');

        return $this->_mapper->fetchAllOrderedWhere('parentId', 'ASC', 'parentId IS NULL');
    }

    public function filterPhotos($data)
    {
        $word      = filter_var($data['name'], FILTER_SANITIZE_STRING);
        $galleryId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
        $images    = $this->_mapper->filterImages($word, $galleryId);
        if (!$images) {
            $this->setMessage(__('no_images_found'));

            return false;
        }
        $this->setJsonData($images->toArray());

        return true;
    }

    public function getGalleryImages($galleryId)
    {
        $images = $this->_mapper->getGalleryImages($galleryId);
        if (!$images) {
            $this->setMessage(__('no_images_found'));

            return false;
        }

        return $images;
    }

    public function sortGalleryImages($galleryId, $data)
    {
        $gallery = $this->getGallery($galleryId);

        if (empty($gallery)) {
            $this->setError(Core_Messages_Message::getMessage('not_isset_gallery'));

            return false;
        }

        return $this->_mapper->sortGalleryImages($gallery->galleryId, $data);
    }

    public function getGallery($id)
    {
        return $this->_mapper->getGallery($id);
    }
}