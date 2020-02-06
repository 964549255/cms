<?php

namespace plugins\cms\controller;

use cmf\controller\PluginRestBaseController;
use think\Db;

class ApiIndexController extends PluginRestBaseController
{
    /*判断子栏目*/
    protected function hasCategorys($id)
    {
        $count = Db::name("cms_category")->where("category_id", $id)->count();
        return $count == 0 ? false : true;
    }

    /*递归-获取所有栏目*/
    protected function getAllCategorys($categorys)
    {
        $categorys->each(function ($data) {
            /*判断类型*/
            switch ($data["type"]) {
                /*栏目*/
                case 1:
                    /*判断子栏目*/
                    if ($this->hasCategorys($data["id"])) {
                        /*加工数据-存在子栏目*/
                        $data["hasCategorys"] = true;
                        /*获取子栏目*/
                        $data["categorys"] = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                            "category_id" => $data["id"],
                            "status" => 1
                        ])->select());
                    } else {
                        /*加工数据-存在子栏目*/
                        $data["hasCategorys"] = false;
                    }
                    break;
                /*链接*/
                case 3:
                    /*获取链接*/
                    $data["url"] = Db::name("cms_link")->where("category_id", $data["id"])->find();
                    break;
            }
            return $data;
        });
        return $categorys;
    }

    /*获取所有栏目*/
    public function getCategorys()
    {
        /*获取所有栏目*/
        $datas = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where([
            "category_id" => 0,
            "status" => 1
        ])->select());
        return zy_json_echo(true, "操作成功", $datas, 200);
    }

    /*获取所有子栏目*/
    public function getSubCategorys()
    {
        /*获取参数*/
        $category_id = input("post.category_id");
        /*判断参数*/
        if (!empty($category_id)) {
            /*获取栏目*/
            $data = Db::name("cms_category")->where("id", $category_id)->find();
            /*判断类型*/
            switch ($data["type"]) {
                /*栏目*/
                case 1:
                    /*判断子栏目*/
                    if ($this->hasCategorys($data["id"])) {
                        /*加工数据-存在子栏目*/
                        $data["hasCategorys"] = true;
                        /*获取子栏目*/
                        $data["categorys"] = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                            "category_id" => $data["id"],
                            "status" => 1
                        ])->select());
                    } else {
                        /*加工数据-存在子栏目*/
                        $data["hasCategorys"] = false;
                    }
                    break;
                /*链接*/
                case 3:
                    /*获取链接*/
                    $data["url"] = Db::name("cms_link")->where("category_id", $data["id"])->find();
                    break;
            }
            return zy_json_echo(true, "操作成功", $data, 200);
        } else {
            return zy_json_echo(false, "栏目编号为空", [], 1);
        }
    }

    /*获取链接*/
    public function getLink()
    {
        /*获取参数*/
        $category_id = input("post.category_id");
        /*判断参数*/
        if (!empty($category_id)) {
            /*获取链接*/
            $data = Db::name("cms_link")->where(["category_id" => $category_id])->find();
            return zy_json_echo(true, "操作成功", $data, 200);
        } else {
            return zy_json_echo(false, "栏目编号为空", [], 1);
        }
    }

    /*获取所有内容*/
    public function getContents()
    {
        /*获取参数*/
        $category_id = input("post.category_id");
        /*判断参数*/
        if (!empty($category_id)) {
            /*获取模型编号*/
            $model_id = Db::name("cms_category")->where(["id" => $category_id])->value("model_id");
            /*判断模型编号*/
            if (empty($model_id)) {
                return zy_json_echo(false, "栏目编号不存在", [], 2);
            }
            /*获取模型键名*/
            $model_field = Db::name("cms_model")->where(["id" => $model_id])->value("field");
            /*获取所有内容*/
            $data["datas"] = Db::name("cms_model_" . $model_field)->where(["category_id" => $category_id])->select();
            /*获取随机内容*/
            $data["random"] = Db::name("cms_model_" . $model_field)->order('rand()')->limit(4)->where(["category_id" => $category_id])->select();
            return zy_json_echo(true, "操作成功", $data, 200);
        } else {
            return zy_json_echo(false, "栏目编号为空", [], 1);
        }
    }

    /*获取内容*/
    public function getContent()
    {
        /*获取参数*/
        $category_id = input("post.category_id");
        $content_id = input("post.content_id");
        /*判断参数*/
        if (!empty($category_id) && !empty($content_id)) {
            /*获取模型编号*/
            $model_id = Db::name("cms_category")->where(["id" => $category_id])->value("model_id");
            /*判断模型编号*/
            if (empty($model_id)) {
                return zy_json_echo(false, "栏目编号不存在", [], 2);
            }
            /*获取模型键名*/
            $model_field = Db::name("cms_model")->where(["id" => $model_id])->value("field");
            /*获取内容*/
            $data["data"] = Db::name("cms_model_" . $model_field)->where(["id" => $content_id])->find();
            /*获取随机内容*/
            $data["random"] = Db::name("cms_model_" . $model_field)->order('rand()')->limit(4)->where([
                "category_id" => $category_id,
                "id" => ["NEQ", $content_id]
            ])->select();
            return zy_json_echo(true, "操作成功", $data, 200);
        } else {
            if (empty($category_id)) {
                return zy_json_echo(false, "栏目编号为空", [], 1);
            }
            if (empty($content_id)) {
                return zy_json_echo(false, "内容编号为空", [], 2);
            }
        }
    }
}