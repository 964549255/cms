<?php

namespace plugins\cms\controller;

use cmf\controller\PluginAdminBaseController;

class AdminIndexController extends PluginAdminBaseController
{
    /**
     * @adminMenu(
     *     'name'   => '内容模块',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '内容模块',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->redirect(cmf_plugin_url("Cms://admin_category/index"));
    }
}