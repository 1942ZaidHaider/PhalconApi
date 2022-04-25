<?php

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
        var_dump($responses);
        die;
    }
    public function orderCreate($event,$context)
    {
        $responses=[];
        $urls=$this->webhookStore->orders->find(["type"=>"add"],['projection'=>["_id"=>0,"url"=>1]])->toArray();
        foreach($urls as $url){
            $curl=curl_init($url['url']);
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,["data"=>json_encode($event->getData())]);
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
