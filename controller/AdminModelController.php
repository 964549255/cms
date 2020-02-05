<?php

namespace plugins\cms\controller;

use cmf\controller\PluginAdminBaseController;
use think\Db;

class AdminModelController extends PluginAdminBaseController
{
    /*默认字段*/
    protected function getFields($model_id)
    {
        /*
         * 1-数值型
         * 2-短文本
         * 3-长文本
         * 4-富文本
         * 5-图片
         * 6-图片组
         */
        return [
            [
                "name" => "缩略图",
                "field" => "thumb",
                "type" => 5,
                "default" => "",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "标题",
                "field" => "title",
                "type" => 2,
                "default" => "",
                "length" => 255,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "关键词",
                "field" => "keyword",
                "type" => 2,
                "default" => "",
                "length" => 255,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "摘要",
                "field" => "summary",
                "type" => 3,
                "default" => "",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "内容",
                "field" => "content",
                "type" => 4,
                "default" => "",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "添加时间",
                "field" => "insert_time",
                "type" => 1,
                "default" => "",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "修改时间",
                "field" => "update_time",
                "type" => 1,
                "default" => "",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "排序",
                "field" => "sort",
                "type" => 1,
                "default" => "1000",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
            [
                "name" => "所属栏目",
                "field" => "category_id",
                "type" => 1,
                "default" => "",
                "length" => 0,
                "vital" => 1,
                "model_id" => $model_id,
            ],
        ];
    }

    /*创建表*/
    protected function getCreateSql($model_field, $fields)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*获取语句*/
        $sql = "";
        $sql .= "CREATE TABLE {$prefix}{$model_field} (";
        $sql .= "id int(10) unsigned NOT NULL AUTO_INCREMENT,";
        foreach ($fields as $field) {
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
            $sql .= "{$field['field']} {$field['type']} {$field['default']} {$field['comment']},";
        }
        $sql .= "PRIMARY KEY (id)";
        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        return $sql;
    }

    /*重名表*/
    protected function getRenameSql($model_field, $model_field_origin)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*获取语句*/
        $sql = "RENAME TABLE {$prefix}{$model_field_origin} TO {$prefix}{$model_field}";
        return $sql;
    }

    /*删除表*/
    protected function getDropSql($model_field)
    {
        /*获取表前缀*/
        $prefix = config("database.prefix") . "cms_model_";
        /*获取语句*/
        $sql = "DROP TABLE IF EXISTS {$prefix}{$model_field}";
        return $sql;
    }

    /*首页*/
    public function index()
    {
        /*获取参数*/
        $query = input("get.");
        $page = input("get.page", 1);
        $rows = input("get.rows", 10);
        $path = cmf_plugin_url("Cms://admin_model/index");
        /*获取数据*/
        $datas = Db::name("cms_model")->order("ID")->paginate([
            "page" => $page,
            "path" => $path,
            "query" => $query,
            "list_rows" => $rows,
            "type" => "bootstrap",
        ]);
        return $this->fetch("", [
            "datas" => $datas
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
            $model_field = $params["field"];
            /*获取模型编号*/
            $model_id = Db::name("cms_model")->insertGetId($params);
            /*获取字段集合*/
            $fields = $this->getFields($model_id);
            /*添加字段集和*/
            foreach ($fields as $field) {
                Db::name("cms_field")->insert($field);
            }
            /*创建表*/
            Db::execute($this->getCreateSql($model_field, $fields));
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
        $data = !empty($id) ? Db::name("cms_model")->where("id", $id)->find() : [];
        /*判断参数*/
        if (!empty($submit)) {
            /*获取模型键名*/
            $model_field = $params["field"];
            /*获取模型原始键名*/
            $model_field_origin = Db::name("cms_model")->where("id", $id)->value("field");
            /*修改模型*/
            Db::name("cms_model")->where("id", $id)->update($params);
            /*判断模型键名变化*/
            if ($model_field != $model_field_origin) {
                /*重名表*/
                Db::execute($this->getRenameSql($model_field, $model_field_origin));
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
            $model_field = Db::name("cms_model")->where("id", $id)->value("field");
            /*删除模型*/
            Db::name("cms_model")->where("id", $id)->delete();
            /*删除字段*/
            Db::name("cms_field")->where("model_id", $id)->delete();
            /*删除表*/
            Db::execute($this->getDropSql($model_field));
            return true;
        }
    }
}