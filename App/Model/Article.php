<?php


namespace App\Model;


use EasySwoole\DDL\Enum\Engine;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;

class Article extends AbstractModel
{
    protected $tableName = "article";

    public function schemaInfo(bool $isCache = true) : Table
    {
        $table = new Table($this->tableName);
        $table->colInt("id")->setIsPrimaryKey(true)->setIsPrimaryKey(true);
        $table->colVarChar("title", "250")->setColumnComment("标题");
        $table->colTinyInt("type", 2)->setColumnComment("类型");
        $table->colText("content")->setColumnComment("内容");
        $table->colInt("create_time", 10)->setColumnComment("创建时间");
        $table->colInt("update_time", 10)->setColumnComment("更新时间");
        $table->indexFullText("content_full_index", "content");
        $table->setTableEngine(Engine::MYISAM);
        return $table;
    }

}