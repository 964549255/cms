<?php

namespace plugins\cms\controller;

use cmf\controller\PluginAdminBaseController;
use think\Db;

class AdminFieldController extends PluginAdminBaseController
{
    /*添加字段*/
    protected function getAddSql($model_field, $field)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*处理默认*/
        if (!empty($field["default"])) {
            $field["default"] = "DEFAULT '{$field['default']}'";
        }
        /*处理类型*/
        switch ($field["type"]) {
            case 1:
                $field["type"] = "int";
                break;
            case 2:
                $field["type"] = "varchar({$field['length']})";
                break;
            case 3:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 4:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 5:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 6:
                $field["type"] = "text";
                $field["default"] = "";
                break;
        }
        /*处理注释*/
        $field["comment"] = "COMMENT '{$field['name']}'";
        /*获取语句*/
        $sql = "ALTER TABLE {$prefix}{$model_field} ADD {$field['field']} {$field['type']} {$field['default']} {$field['comment']}";
        return $sql;
    }

    /*修改字段 重名字段*/
    protected function getChangeSql($model_field, $field)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*处理默认*/
        if (!empty($field["default"])) {
            $field["default"] = "DEFAULT '{$field['default']}'";
        }
        /*处理类型*/
        switch ($field["type"]) {
            case 1:
                $field["type"] = "int";
                break;
            case 2:
                $field["type"] = "varchar({$field['length']})";
                break;
            case 3:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 4:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 5:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 6:
                $field["type"] = "text";
                $field["default"] = "";
                break;
        }
        /*处理注释*/
        $field["comment"] = "COMMENT '{$field['name']}'";
        /*获取语句*/
        $sql = "ALTER TABLE {$prefix}{$model_field} CHANGE {$field['field_origin']} {$field['field']} {$field['type']} {$field['default']} {$field['comment']}";
        return $sql;
    }

    /*修改字段 不重名字段*/
    protected function getModifySql($model_field, $field)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*处理默认*/
        if (!empty($field["default"])) {
            $field["default"] = "DEFAULT '{$field['default']}'";
        }
        /*处理类型*/
        switch ($field["type"]) {
            case 1:
                $field["type"] = "int";
                break;
            case 2:
                $field["type"] = "varchar({$field['length']})";
                break;
            case 3:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 4:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 5:
                $field["type"] = "text";
                $field["default"] = "";
                break;
            case 6:
                $field["type"] = "text";
                $field["default"] = "";
                break;
        }
        /*处理注释*/
        $field["comment"] = "COMMENT '{$field['name']}'";
        /*获取语句*/
        $sql = "ALTER TABLE {$prefix}{$model_field} MODIFY {$field['field']} {$field['type']} {$field['default']} {$field['comment']}";
        return $sql;
    }

    /*删除字段*/
    protected function getDropSql($model_field, $field_field)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*获取语句*/
        $sql = "ALTER TABLE {$prefix}{$model_field} DROP {$field_field}";
        return $sql;
    }

    /*首页*/
    public function index()
    {
        /*获取参数*/
        $model_id = input("get.model_id");
        $query = input("get.");
        $page = input("get.page", 1);
        $rows = input("get.rows", 10);
        $path = cmf_plugin_url("Cms://admin_field/index");
        /*获取数据*/
        $datas = Db::name("cms_field")->order("SORT, ID")->where("model_id", $model_id)->paginate([
            "page" => $page,
            "path" => $path,
            "query" => $query,
            "list_rows" => $rows,
            "type" => "bootstrap",
        ])->each(function ($data) {
            /*加工数据-类型*/
            switch ($data["type"]) {
                case 1:
                    $data["type_text"] = "数值型";
                    break;
                case 2:
                    $data["type_text"] = "短文本";
                    break;
                case 3:
                    $data["type_text"] = "长文本";
                    break;
                case 4:
                    $data["type_text"] = "富文本";
                    break;
                case 5:
                    $data["type_text"] = "图片";
                    break;
                case 6:
                    $data["type_text"] = "图片组";
                    break;
            }
            return $data;
        });
        return $this->fetch("", [
            "datas" => $datas,
            "model_id" => $model_id,
        ]);
    }

    /*添加*/
    public function add()
    {
        /*获取参数*/
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*判断参数*/
        if (!empty($submit)) {
            /*获取模型键名*/
            $model_field = Db::name("cms_model")->where("id", $params["model_id"])->value("field");
            /*添加字段*/
            Db::name("cms_field")->insert($params);
            /*添加字段*/
            Db::execute($this->getAddSql($model_field, $params));
            return true;
        } else {
            return $this->fetch();
        }
    }

    /*修改*/
    public function edit()
    {
        /*获取参数*/
        $id = input("get.id");
        $submit = input("post.submit");
        $params = input("post.params/a");
        /*获取数据*/
        $data = !empty($id) ? Db::name("cms_field")->where("id", $id)->find() : [];
        /*判断参数*/
        if (!empty($submit)) {
            /*处理长度*/
            if (empty($params["length"])) {
                $params["length"] = 0;
            }
            /*获取模型键名*/
            $model_field = Db::name("cms_model")->where("id", $params["model_id"])->value("field");
            /*获取字段键名*/
            $field_field = $params["field"];
            /*获取字段原始键名*/
            $field_field_origin = Db::name("cms_field")->where("id", $id)->value("field");
            /*修改字段*/
            Db::name("cms_field")->where("id", $id)->update($params);
            /*判断字段键名变化*/
            if ($field_field != $field_field_origin) {
                $params["field_origin"] = $field_field_origin;
                /*修改字段 重名字段*/
                Db::execute($this->getChangeSql($model_field, $params));
            } else {
                /*修改字段 不重名字段*/
                Db::execute($this->getModifySql($model_field, $params));
            }
            return true;
        } else {
            return $this->fetch("", [
                "data" => $data,
            ]);
        }
    }

    /*删除*/
    public function delete()
    {
        /*获取参数*/
        $id = input("get.id");
        /*判断参数*/
        if (!empty($id)) {
            /*获取模型键名*/
            $model_field = Db::name("cms_model")->where("id", Db::name("cms_field")->where("id", $id)->value("model_id"))->value("field");
            /*获取字段键名*/
            $field_field = Db::name("cms_field")->where("id", $id)->value("field");
            /*删除字段*/
            Db::name("cms_field")->where("id", $id)->delete();
            /*删除表*/
            Db::execute($this->getDropSql($model_field, $field_field));
            return true;
        }
    }

    /*状态*/
    public function status()
    {
        /*获取参数*/
        $id = input("get.id");
        $status = input("get.status");
        /*判断参数*/
        if (!empty($id) && !empty($status)) {
            /*修改字段状态*/
            Db::name("cms_field")->where("id", $id)->update(["status" => $status]);
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
            /*修改字段排序*/
            Db::name("cms_field")->where("id", $id)->update(["sort" => $sort]);
            return true;
        }
    }
}