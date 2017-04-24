<?php

namespace Famousinteractive\ContentApi\Library;

use GuzzleHttp\Client;
use Log;

class Api
{
    protected static $_instance = null;

    protected $client = null;
    protected $request = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * @return Api
     */
    public static function getApi() {

        if(is_null(self::$_instance)) {
            return new self();
        }
        return self::$_instance;
    }

    public function initCall() {

        $this->client = new Client([
            'base_uri'  => config('famousContentApi.apiUrl'),
        ]);

    }

    public function getBy($key, $language) {
        $this->initCall();
        try {
            $param = http_build_query([
                'clientId'  => config('famousContentApi.clientId'),
                'key'       => $key,
                'lang'      => $language
            ]);

            $this->request = $this->client->request('GET', config('famousContentApi.apiEndpoint').'?'.$param, [
                'headers'   => [
                    'apiKey'    => config('famousContentApi.key')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        return $this->request->getBody()->getContents();
    }


    public function pushMissingTranslation($key, $language, $default) {
        $this->initCall();
        try {
            $this->request = $this->client->request('POST', config('famousContentApi.apiEndpoint'), [
                'form_params'  => [
                    'clientId' => config('famousContentApi.clientId'),
                    'key'      => $key,
                    'value'    => $default,
                    'lang'     => $language
                ],
                'headers'   => [
                    'apiKey'    => config('famousContentApi.key')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        return $this->request->getBody()->getContents();
    }


}