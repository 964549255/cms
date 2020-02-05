<?php

namespace plugins\cms;

use cmf\lib\Plugin;

class CmsPlugin extends Plugin
{
    public $info = [
        'name' => 'Cms',
        'title' => '内容模块',
        'author' => '徐灵峰',
        'version' => '1.0.0',
        'description' => '内容模块',
        'status' => 1
    ];

    public $hasAdmin = 1;

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
}