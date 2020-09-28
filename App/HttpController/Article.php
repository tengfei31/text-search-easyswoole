<?php
namespace App\HttpController;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;

class Article extends Controller 
{
    public function searchArticle()
    {
        $query = new QueryBuilder();
        $query->raw("select * from article");
        $data = DbManager::getInstance()->query($query, true)->getResult();
        return $this->writeJson(200, $data, "success");
    }
}