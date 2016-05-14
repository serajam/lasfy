<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Voronoy
 * Date: 27.08.13
 * Time: 20:27
 * To change this template use File | Settings | File Templates.
 */
class Admin_SettingsController extends Core_Controller_Editor
{
    /**
     * Default service class name for current controller
     *
     * @var String
     */
    protected $_defaultServiceName = 'SettingsService';

    /**
     * The service layer object
     *
     * @var SettingsService
     */
    protected $_service;

    protected $_htmlContainer = 'settings-list';

    public function indexAction()
    {
    }
}