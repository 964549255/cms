<?php

namespace plugins\cms\controller;

use cmf\controller\PluginAdminBaseController;
use think\Db;
use think\Request;

class AdminContentController extends PluginAdminBaseController
{
    /*判断子链接*/
    protected function hasLink($category_id)
    {
        $count = Db::name("cms_link")->where("category_id", $category_id)->count();
        return $count == 0 ? false : true;
    }

    /*判断子栏目*/
    protected function hasCategorys($id)
    {
        $count = Db::name("cms_category")->where("category_id", $id)->count();
        return $count == 0 ? false : true;
    }

    /*递归-获取所有栏目（所有类型）*/
    protected function getAllCategorys($categorys)
    {
        $categorys->each(function ($data) {
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = true;
                /*加工数据-子栏目*/
                $data["categorys"] = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                    "category_id" => $data["id"],
                    "status" => 1
                ])->select());
                return $data;
            } else {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = false;
                return $data;
            }
        });
        return $categorys;
    }

    /*首页*/
    public function index()
    {
        /*获取数据*/
        $datas = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where([
            "category_id" => 0,
            "status" => 1
        ])->select());
        return $this->fetch("", [
            "datas" => $datas,
        ]);
    }

    /*展示类型-首页*/
    public function show_index()
    {
        return $this->fetch();
    }

    /*展示类型-栏目*/
    public function show()
    {
        return $this->fetch();
    }

    /*展示类型-链接*/
    public function link()
    {
        /*获取参数*/
        $category_id = input("get.category_id");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取数据*/
        $category = !empty($category_id) ? Db::name("cms_category")->where("id", $category_id)->find() : [];
        $data = !empty($category_id) ? Db::name("cms_link")->where("category_id", $category_id)->find() : [];
        /*判断参数*/
        if (!empty($submit)) {
            /*判断子链接*/
            if ($this->hasLink($category_id)) {
                /*修改数据*/
                Db::name("cms_link")->where("category_id", $category_id)->update($params);
            } else {
                /*添加数据*/
                Db::name("cms_link")->insert($params);
            }
            $this->redirect(cmf_plugin_url('Cms://admin_content/link') . "?category_id={$category_id}");
        } else {
            return $this->fetch("", [
                "data" => $data,
                "category" => $category,
            ]);
        }
    }

    public function uploadFile($file, $path)
    {
        if ($file) {
            $info = $file->move($path);
            if ($info) {
                return $info->getSaveName();
            }
        }
    }

    /*文件上传*/
    public function upload(Request $request)
    {
        $filename = $this->uploadFile($request->file('file'), ROOT_PATH . '/public/upload');
        $filename = str_replace("\\", "/", $filename);
        return json(['filename' => $request->domain() . '/upload/' . $filename]);
    }
}