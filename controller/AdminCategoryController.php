<?php

namespace plugins\cms\controller;

use cmf\controller\PluginAdminBaseController;
use think\Db;
use think\Request;

class AdminCategoryController extends PluginAdminBaseController
{
    /*判断子栏目*/
    protected function hasCategorys($id)
    {
        $count = Db::name("cms_category")->where("category_id", $id)->count();
        return $count == 0 ? false : true;
    }

    /*递归-获取所有栏目（所有类型）*/
    protected function getAllCategorys($categorys, $level)
    {
        $categorys->each(function ($data) use ($level) {
            /*加工数据-名称*/
            if ($level > 1) {
                $data["name"] = "| " . str_repeat("-- ", $level - 1) . $data["name"];
            }
            /*加工数据-类型*/
            switch ($data["type"]) {
                case 1:
                    $data["type_text"] = "栏目";
                    break;
                case 2:
                    $data["type_text"] = "列表";
                    break;
                case 3:
                    $data["type_text"] = "链接";
                    break;
            }
            /*加工数据-所属模型*/
            $data["model_name"] = Db::name("cms_model")->where("id", $data["model_id"])->value("name");
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = true;
                /*加工数据-子栏目*/
                $data["categorys"] = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where("category_id", $data["id"])->select(), ++$level);
                return $data;
            } else {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = false;
                return $data;
            }
        });
        return $categorys;
    }

    /*递归-获取所有栏目（栏目类型）*/
    protected function getCateCategorys($categorys, $level)
    {
        $categorys->each(function ($data) use ($level) {
            /*加工数据-名称*/
            if ($level > 1) {
                $data["name"] = "| " . str_repeat("-- ", $level - 1) . $data["name"];
            }
            /*加工数据-类型*/
            switch ($data["type"]) {
                case 1:
                    $data["type_text"] = "栏目";
                    break;
                case 2:
                    $data["type_text"] = "列表";
                    break;
                case 3:
                    $data["type_text"] = "链接";
                    break;
            }
            /*加工数据-所属模型*/
            $data["model_name"] = Db::name("cms_model")->where("id", $data["model_id"])->value("name");
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = true;
                /*加工数据-子栏目*/
                $data["categorys"] = $this->getCateCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                    "category_id" => $data["id"],
                    "type" => 1
                ])->select(), ++$level);
                return $data;
            } else {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = false;
                return $data;
            }
        });
        return $categorys;
    }

    /*递归-获取其他所有栏目（栏目类型）*/
    protected function getOtherCateCategorys($categorys, $level, $id)
    {
        $categorys->each(function ($data) use ($level, $id) {
            /*加工数据-名称*/
            if ($level > 1) {
                $data["name"] = "| " . str_repeat("-- ", $level - 1) . $data["name"];
            }
            /*加工数据-类型*/
            switch ($data["type"]) {
                case 1:
                    $data["type_text"] = "栏目";
                    break;
                case 2:
                    $data["type_text"] = "列表";
                    break;
                case 3:
                    $data["type_text"] = "链接";
                    break;
            }
            /*加工数据-所属模型*/
            $data["model_name"] = Db::name("cms_model")->where("id", $data["model_id"])->value("name");
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = true;
                /*加工数据-子栏目*/
                $data["categorys"] = $this->getOtherCateCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                    "category_id" => $data["id"],
                    "type" => 1,
                    "id" => ["NEQ", $id],
                ])->select(), ++$level, $id);
                return $data;
            } else {
                /*加工数据-存在子栏目*/
                $data["hasCategorys"] = false;
                return $data;
            }
        });
        return $categorys;
    }

    /*递归-修改栏目模型*/
    protected function executeModelId($categorys, $model_id)
    {
        $categorys->each(function ($data) use ($model_id) {
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*处理子栏目*/
                $this->executeModelId(Db::name("cms_category")->where("category_id", $data["id"])->select(), $model_id);
            }
            /*处理栏目*/
            Db::name("cms_category")->where("id", $data["id"])->update(["model_id" => $model_id]);
        });
    }

    /*首页*/
    public function index()
    {
        /*获取参数*/
        $query = input("get.");
        $page = input("get.page", 1);
        $rows = input("get.rows", 10);
        $path = cmf_plugin_url("Cms://admin_category/index");
        /*获取数据*/
        $datas = $this->getAllCategorys(Db::name("cms_category")->order("SORT, ID")->where("category_id", 0)->paginate([
            "page" => $page,
            "path" => $path,
            "query" => $query,
            "list_rows" => $rows,
            "type" => "bootstrap",
        ]), 1);
        return $this->fetch("", [
            "datas" => $datas,
        ]);
    }

    /*添加*/
    public function add()
    {
        /*获取参数*/
        $category_id = input("get.category_id");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取数据*/
        $model_id = !empty($category_id) ? Db::name("cms_category")->where("id", $category_id)->value("model_id") : 0;
        /*判断参数*/
        if (!empty($submit)) {
            /*添加栏目*/
            Db::name("cms_category")->insert($params);
            return true;
        } else {
            /*获取所有模型*/
            $models = Db::name("cms_model")->order("ID")->select();
            /*递归-获取所有栏目（栏目类型）*/
            $categorys = $this->getCateCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                "category_id" => 0,
                "type" => 1
            ])->select(), 1);
            return $this->fetch("", [
                "category_id" => $category_id,
                "model_id" => $model_id,
                "models" => $models,
                "categorys" => $categorys,
            ]);
        }
    }

    /*修改*/
    public function edit()
    {
        /*获取参数*/
        $id = input("get.id");
        $sync = input("post.sync");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取数据*/
        $data = !empty($id) ? Db::name("cms_category")->where("id", $id)->find() : [];
        if (!empty($submit)) {
            /*修改栏目*/
            Db::name("cms_category")->where("id", $id)->update($params);
            /*修改所属模型*/
            if ($sync == 1) {
                $model_id = $params["model_id"];
                /*判断子栏目*/
                if ($this->hasCategorys($id)) {
                    /*处理子栏目*/
                    $this->executeModelId(Db::name("cms_category")->where("category_id", $id)->select(), $model_id);
                }
            }
            return true;
        } else {
            /*获取所有模型*/
            $models = Db::name("cms_model")->order("ID")->select();
            /*递归-获取其他所有栏目（栏目类型）*/
            $categorys = $this->getOtherCateCategorys(Db::name("cms_category")->order("SORT, ID")->where([
                "category_id" => 0,
                "type" => 1,
                "id" => ["NEQ", $id],
            ])->select(), 1, $id);
            return $this->fetch("", [
                "data" => $data,
                "models" => $models,
                "categorys" => $categorys,
                "hasCategorys" => $this->hasCategorys($id)
            ]);
        }
    }

    /*SEO*/
    public function seo()
    {
        /*获取参数*/
        $id = input("get.id");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取数据*/
        $data = !empty($id) ? Db::name("cms_category")->where("id", $id)->find() : [];
        /*判断参数*/
        if (!empty($submit)) {
            /*修改SEO*/
            Db::name("cms_category")->where("id", $id)->update($params);
            return true;
        } else {
            return $this->fetch("", [
                "data" => $data,
            ]);
        }
    }

    /*递归-删除栏目*/
    protected function executeDelete($categorys)
    {
        $categorys->each(function ($data) {
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*处理子栏目*/
                $this->executeDelete(Db::name("cms_category")->where("category_id", $data["id"])->select());
            }
            /*处理栏目*/
            Db::name("cms_category")->where("id", $data["id"])->delete();
        });
    }

    /*删除*/
    public function delete()
    {
        /*获取参数*/
        $id = input("get.id");
        /*判断参数*/
        if (!empty($id)) {
            /*删除栏目*/
            Db::name("cms_category")->where("id", $id)->delete();
            /*判断子栏目*/
            if ($this->hasCategorys($id)) {
                /*处理子栏目*/
                $this->executeDelete(Db::name("cms_category")->where("category_id", $id)->select());
            }
            return true;
        }
    }

    /*递归-修改栏目状态*/
    protected function executeStatus($categorys, $status)
    {
        $categorys->each(function ($data) use ($status) {
            /*判断子栏目*/
            if ($this->hasCategorys($data["id"])) {
                /*处理子栏目*/
                $this->executeStatus(Db::name("cms_category")->where("category_id", $data["id"])->select(), $status);
            }
            /*处理栏目*/
            Db::name("cms_category")->where("id", $data["id"])->update(["status" => $status]);
        });
    }

    /*状态*/
    public function status()
    {
        /*获取参数*/
        $id = input("get.id");
        $status = input("get.status");
        /*判断参数*/
        if (!empty($id) && !empty($status)) {
            /*修改栏目状态*/
            Db::name("cms_category")->where("id", $id)->update(["status" => $status]);
            /*判断子栏目*/
            if ($this->hasCategorys($id)) {
                /*处理子栏目*/
                $this->executeStatus(Db::name("cms_category")->where("category_id", $id)->select(), $status);
            }
            return true;
        }
    }

    /*排序*/
    public function sort()
    {
        /*获取参数*/
        $id = input("get.id");
        $sort = input("get.sort");
        /*判断参数*/
        if (!empty($id) && !empty($sort)) {
            /*修改栏目排序*/
            Db::name("cms_category")->where("id", $id)->update(["sort" => $sort]);
            return true;
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