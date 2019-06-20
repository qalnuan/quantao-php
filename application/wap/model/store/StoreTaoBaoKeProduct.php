<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/12
 */

namespace app\wap\model\store;

use basic\ModelBasic;
use traits\ModelTrait;

class StoreTaoBaoKeProduct extends ModelBasic
{
    use  ModelTrait;

    public static function getNinePointNineProduct($pageno = 0, $pagesize = 20)
    {
        vendor("taobaoke.TopSdk");
        $c = new \TopClient;
        $c->appkey = "27585457";
        $c->secretKey = "ed9b8b265edba406760e0958d8f3094a";
        $c->format = "json";
        $req = new \TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ("9.9");
        $req->setPageNo($pageno);
        $req->setPageSize($pagesize);
        $resp = $c->execute($req);
        $results = $resp->results;
        $product_list = $results->n_tbk_item;
        return $product_list;
    }

}