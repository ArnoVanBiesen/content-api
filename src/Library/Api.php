<?php

namespace Famousinteractive\ContentApi\Library;

use GuzzleHttp\Client;
use Log;

/**
 * Class Api
 * @package Famousinteractive\ContentApi\Library
 */
class Api
{
    protected static $_instance = null;
    protected $client = null;
    protected $request = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * @return Api|null
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

    /**
     * @param $key
     * @param $language
     * @return bool|string
     */
    public function getBy($key, $language) {

        $this->initCall();
        try {
            $param = http_build_query([
                'clientId'  => config('famousContentApi.clientId'),
                'key'       => $key,
                'lang'      => $language,
                'onlyValue' => true,
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

        $content = json_decode($this->request->getBody()->getContents(), true);

        return  isset($content['data'][0]['value']) ? $content['data'][0]['value'] : '';
    }

    /**
     * @param $key
     * @param $language
     * @param $default
     * @return bool
     */
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

        $content = json_decode($this->request->getBody()->getContents(), true);

        if(!isset($content['success']) || !$content['success'] ) {
            Log::error('Error while pushing translation with message ' . $content['message']);
            return false;
        }
        return true;
    }

    /**
     * @param $datasetName
     * @param string $param
     * @return bool|string
     */
    public function getDataset($datasetName, $param='all') {

        $this->initCall();
        try {
            $param = http_build_query([
                'clientId'  => config('famousContentApi.clientId'),
                'param'     => $param,
            ]);

            $this->request = $this->client->request('GET', config('famousContentApi.apiDatasetEndpoint').'/'.str_slug($datasetName).'?'.$param, [
                'headers'   => [
                    'apiKey'    => config('famousContentApi.key')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        $content = json_decode($this->request->getBody()->getContents(), true);

        return  isset($content['data']) ? $content['data'] : '';
    }

    /**
     * @param $datasetName
     * @param $data
     * @return bool
     */
    public function putDatasetRecord($datasetName, $data) {

        $this->initCall();
        $formParams = [];
        $formParams['fields'] = $data;
        $formParams['clientId'] = config('famousContentApi.clientId');

        try {
            $this->request = $this->client->request('POST', config('famousContentApi.apiDatasetEndpoint').'/'.str_slug($datasetName), [
                'form_params'  => $formParams,
                'headers'   => [
                    'apiKey'    => config('famousContentApi.key')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        $content = json_decode($this->request->getBody()->getContents(), true);

        if(!isset($content['success']) || !$content['success'] ) {
            Log::error('Error while pushing translation with message ' . $content['message']);
            return $content['message'];
        }
        return true;
    }
}