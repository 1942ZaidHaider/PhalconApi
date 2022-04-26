<?php

use MongoDB\BSON\ObjectId;

/**
 * Handler for Webhooks
 */
class UpdatesListener extends Phalcon\Di\Injectable
{
    public function productInsert($event,$context)
    {
        $responses=[];
        $urls=$this->webhookStore->product->find(["type"=>"add"],['projection'=>["_id"=>0,"url"=>1]])->toArray();
        foreach($urls as $url){
            $curl=curl_init($url['url']);
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,["data"=>json_encode($event->getData())]);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
            $responses[]=curl_exec($curl);
        }
    }
    public function productUpdate($event,$context)
    {
        $responses=[];
        $urls=$this->webhookStore->product->find(["type"=>"update"],['projection'=>["_id"=>0,"url"=>1]])->toArray();
        foreach($urls as $url){
            $curl=curl_init($url['url']);
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,["data"=>json_encode($event->getData())]);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
            $responses[]=curl_exec($curl);
        }
    }
    public function orderCreate($event,$context)
    {
        $order=$event->getData();
        $resp=$this->mongo->products->updateOne(["_id"=>new ObjectId($order["product_id"])],['$inc'=>["stock"=>-1]]);
        $this->events->fire("Updates:productUpdate",$context,$this->mongo->products->findOne(["_id"=>new ObjectId($order["product_id"])]));
        print_r($resp);
        $responses=[];
        $urls=$this->webhookStore->orders->find(["type"=>"add"],['projection'=>["_id"=>0,"url"=>1]])->toArray();
        foreach($urls as $url){
            $curl=curl_init($url['url']);
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,["data"=>json_encode($order)]);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
            $responses[]=curl_exec($curl);
        }
    }
    public function orderUpdate($event,$context)
    {
        $responses=[];
        $urls=$this->webhookStore->orders->find(["type"=>"update"],['projection'=>["_id"=>0,"url"=>1]])->toArray();
        foreach($urls as $url){
            $curl=curl_init($url['url']);
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,["data"=>json_encode($event->getData())]);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
            $responses[]=curl_exec($curl);
        }
    }
}
