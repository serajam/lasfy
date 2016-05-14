<?php
use Imagecow\Image;

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 04.09.13
 * Time: 21:58
 * To change this template use File | Settings | File Templates.
 */
class Core_Image_Manager
{
    /**
     * @var default image size name
     */
    const DEFAULT_SIZE = 'default';

    /**
     * @var current compression size
     */
    protected static $_compress;

    /**
     * @var current server image name
     */
    protected static $_imageName;

    /**
     * @var current image name
     */
    protected static $_name;

    /**
     * @var string default convert type
     */
    protected static $_convertType = 'jpg';

    /**
     * @var array compression sizes
     */
    protected $_sizes = [];

    /**
     * @var array  images
     */
    protected $_stack = [];

    /**
     * @var bool rename flag
     */
    protected $_rename = true;

    /**
     * @var Core_Collection_ImagesCollection
     */
    protected $_collection;

    /**
     * construct
     */
    public function __construct()
    {
        $this->_collection = new Core_Image_Collection_Images();
    }

    /**
     * @param $imgInfo Info image from adapter
     *                 add info to images stack
     */
    public function addStack($imgInfo)
    {
        $this->_stack[] = $imgInfo;
    }

    /**
     * @param array $sizes convert sizes
     */
    public function setSizes(array $sizes)
    {
        $this->_sizes = $sizes;
    }

    /**
     * @param string  type
     */
    public function setConvertType($type)
    {
        self::$_convertType = $type;
    }

    public function runProcess()
    {
        if (empty($this->_stack)) {
            $this->error = __('stack_is_empty');

            return false;
        }

        $handler = Image::create();
        $handler->setCompressionQuality(80);
        $handler->format(self::$_convertType);

        if (!empty($this->_sizes)) {
            foreach ($this->_sizes as $key => $compress) {
                foreach ($this->_stack as $image) {
                    $handler->load($image['tmp_name']);
                    $this->randRename($image['name'], $key);
                    $destination = BASE_PATH . '/uploads/';
                    if (!file_exists($destination)) {
                        mkdir($destination, 0777, 1);
                    }

                    // list($width, $height) = getimagesize($image['tmp_name']);
                    list($resizeWidth, $resizeHeight) = explode('-', $compress);
                    $this->setCompression($resizeWidth . '-' . $resizeHeight);

                    if ($resizeWidth > 0 && $resizeHeight > 0) { // resize and crop
                        $handler->resizeCrop($resizeWidth, $resizeHeight, 'center', 'middle');
                    } elseif ($resizeWidth == 0 && $resizeHeight > 0) { // ratio resize according width
                        $handler->resize($resizeWidth, $resizeHeight, 1);
                    } elseif ($resizeWidth == 0 && $resizeHeight > 0) { //  ratio resize according height
                        $handler->resize($resizeWidth, $resizeHeight, 1);
                    }

                    $res = $handler->save($destination . self::$_imageName);

                    if ($res) {
                        $this->addDataToCollection($key);
                    }
                }
            }
        }

        return $this->_collection;
    }

    public function randRename($name, $additional = false)
    {
        $name    = preg_replace('/\.\w+$/', '', $name);
        $newName = $this->_randString() . '_' /* . $name .
            '_'*/
        ;
        if ($additional) {
            $newName .= $additional;
        }
        $this->file_new_name_body = $newName;
        $this->file_safe_name     = true;
        $type                     = self::$_convertType;
        $newName .= '.' . $type;
        $this->setNames($name, $newName);
    }

    protected function _randString($length = 10)
    {
        $config = Zend_Registry::get('appConfig');
        $chars  = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        /*$chars .= $config['salt'];*/
        $numChars = strlen($chars);
        $string   = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(5, $numChars) - 1, 1);
        }

        return $string;
    }

    protected function setNames($name, $newName)
    {
        self::$_name      = preg_replace('/\.\w+$/', '', $name);
        self::$_imageName = $newName;
    }

    protected function setCompression($compression)
    {
        self::$_compress = $compression;
    }

    protected function addDataToCollection($sizeType = 0)
    {
        $data = [
            'name'          => self::$_name,
            'imageName'     => self::$_imageName,
            'compression'   => self::$_compress,
            'imageSizeType' => $sizeType,
        ];
        $this->_collection->add($data);
    }

    public function processLogo($image)
    {
        $handler = Image::create();
        $handler->load($image['file']['tmp_name']);
        $handler->setCompressionQuality(80);
        $handler->format(self::$_convertType);
        $handler->resize(150, 150, 1);
        $destination = BASE_PATH . '/uploads/';
        if (!file_exists($destination)) {
            mkdir($destination, 0777, 1);
        }

        $filename = mt_rand() . mt_rand();
        $ext      = pathinfo($image['file']['tmp_name'], PATHINFO_EXTENSION);
        $handler->save($destination . $filename . '.' . $ext);
        $file = $destination . $filename . '.' . $ext;
        $res  = file_get_contents($file);
        unlink($file);

        return $res;
    }
}