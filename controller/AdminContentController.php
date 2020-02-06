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

    /*内容*/
    public function lists()
    {
        /*获取参数*/
        $category_id = input("get.category_id");
        /*获取模型编号*/
        $model_id = Db::name("cms_category")->where("id", $category_id)->value("model_id");
        /*获取模型键名*/
        $model_field = Db::name("cms_model")->where("id", $model_id)->value("field");
        /*获取字段集合*/
        $fields = Db::name("cms_field")->field("name, field, type")->order("SORT, ID")->where([
            "model_id" => $model_id,
            "status" => 1
        ])->select();
        /*获取参数*/
        $query = input("get.");
        $page = input("get.page", 1);
        $rows = input("get.rows", 10);
        $path = cmf_plugin_url("Cms://admin_content/lists");
        /*获取数据*/
        $datas = Db::name("cms_model_" . $model_field)->order("SORT, ID")->where("category_id", $category_id)->paginate([
            "page" => $page,
            "path" => $path,
            "query" => $query,
            "list_rows" => $rows,
            "type" => "bootstrap",
        ])->each(function ($data) {
            /*加工数据-添加时间*/
            $data["insert_time_text"] = date("Y-m-d h:i:s", $data["insert_time"]);
            /*加工数据-修改时间*/
            $data["update_time_text"] = date("Y-m-d h:i:s", $data["update_time"]);
            return $data;
        });
        return $this->fetch("", [
            "datas" => $datas,
            "fields" => $fields,
            "category_id" => $category_id,
        ]);
    }

    /*添加内容*/
    public function addLists()
    {
        /*获取参数*/
        $category_id = input("get.category_id");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取模型编号*/
        $model_id = Db::name("cms_category")->where("id", $category_id)->value("model_id");
        /*获取模型键名*/
        $model_field = Db::name("cms_model")->where("id", $model_id)->value("field");
        /*获取字段集合*/
        $fields = Db::name("cms_field")->field("name, field, type")->order("SORT, ID")->where([
            "model_id" => $model_id,
            "status" => 1
        ])->select();
        /*判断参数*/
        if (!empty($submit)) {
            /*设置参数*/
            /*
            foreach ($fields as $key => $value) {
                if ($value["type"] == 4) {
                    if (isset($params[$value["field"]])) {
                        $params[$value["field"]] = htmlspecialchars_decode($params[$value["field"]]);
                    }
                }
            }
            */
            foreach ($fields as $field) {
                switch ($field["type"]) {
                    case 6:
                        if (!empty($params[$field["field"]])) {
                            $params[$field["field"]] = implode(", ", $params[$field["field"]]);
                        } else {
                            $params[$field["field"]] = "";
                        }
                        break;
                }
            }
            $params["insert_time"] = time();
            $params["update_time"] = time();
            $params["category_id"] = $category_id;
            $params["author"] = cmf_get_current_admin_name();
            /*添加数据-对象表*/
            Db::name("cms_model_" . $model_field)->insert($params);
            return true;
        } else {
            return $this->fetch("", [
                "fields" => $fields,
                "category_id" => $category_id
            ]);
        }
    }

    public function editLists()
    {
        /*获取参数*/
        $id = input("get.id");
        $category_id = input("get.category_id");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取模型编号*/
        $model_id = Db::name("cms_category")->where("id", $category_id)->value("model_id");
        /*获取模型键名*/
        $model_field = Db::name("cms_model")->where("id", $model_id)->value("field");
        /*获取字段集合*/
        $fields = Db::name("cms_field")->field("name, field, type")->order("SORT, ID")->where([
            "model_id" => $model_id,
            "status" => 1
        ])->select();
        /*获取数据*/
        $data = !empty($id) ? Db::name("cms_model_" . $model_field)->where("id", $id)->find() : [];
        /*判断参数*/
        if (!empty($submit)) {
            /*设置参数*/
            foreach ($fields as $field) {
                switch ($field["type"]) {
                    case 6:
                        if (!empty($params[$field["field"]])) {
                            $params[$field["field"]] = implode(", ", $params[$field["field"]]);
                        } else {
                            $params[$field["field"]] = "";
                        }
                        break;
                }
            }
            $params["update_time"] = time();
            /*修改数据-对象表*/
            Db::name("cms_model_" . $model_field)->where("id", $id)->update($params);
            return true;
        } else {
            return $this->fetch("", [
                "data" => $data,
                "fields" => $fields,
                "category_id" => $category_id,
            ]);
        }
    }

    /*删除内容*/
    public function deleteLists()
    {
        /*获取参数*/
        $id = input("get.id");
        $category_id = input("get.category_id");
        /*获取模型编号*/
        $model_id = Db::name("cms_category")->where("id", $category_id)->value("model_id");
        /*获取模型键名*/
        $model_field = Db::name("cms_model")->where("id", $model_id)->value("field");
        /*判断参数*/
        if (!empty($id)) {
            /*删除数据-对象表*/
            Db::name("cms_model_" . $model_field)->where("id", $id)->delete();
            return true;
        }
    }

    /*排序内容*/
    public function sortLists()
    {
        /*获取参数*/
        $id = input("get.id");
        $sort = input("get.sort");
        $category_id = input("get.category_id");
        /*获取模型编号*/
        $model_id = Db::name("cms_category")->where("id", $category_id)->value("model_id");
        /*获取模型键名*/
        $model_field = Db::name("cms_model")->where("id", $model_id)->value("field");
        /*判断参数*/
        if (!empty($id) && !empty($sort)) {
            /*修改数据*/
            Db::name("cms_model_" . $model_field)->where("id", $id)->update(["sort" => $sort]);
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