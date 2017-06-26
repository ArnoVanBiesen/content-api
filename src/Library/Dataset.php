<?php

namespace Famousinteractive\ContentApi\Library;
use Illuminate\Support\Facades\Cache;

/**
 * Class Dataset
 * @package Famousinteractive\ContentApi\Library
 */
class Dataset
{
    /**
     * @param $datasetName
     * @param bool $prefixLang
     * @param string $param
     * @param bool $preferCache
     * @return bool|string
     */
    public static function get($datasetName, $prefixLang=false, $param='all', $preferCache=true) {

        $instance = new self();
        $api = Api::getApi();

        if($prefixLang) {
            $datasetName = $instance->getCurrentLang().'-'.$datasetName;
        }

        $datasetName = str_slug($datasetName);

        $param = strtolower($param);
        if(empty($param) || !in_array($param, ['all','datas','fields','formatted'])) {
            $param = 'all';
        }

        if($preferCache) {
            $value = Cache::remember('cache-fitdataset-' . $datasetName.'-'.$param, 3600, function () use ($instance, $datasetName, $api, $param) {
                return $api->getDataset($datasetName, $param);
            });
        } else {
            $value = $api->getDataset($datasetName, $param);
        }

        return $value;
    }

    /**
     * @param $datasetName
     * @param array $data
     * @param bool $prefixLang
     * @return bool
     */
    public static function put($datasetName, $data = array(), $prefixLang=false) {

        $instance = new self();
        $api = Api::getApi();

        if($prefixLang) {
            $datasetName = $instance->getCurrentLang().'-'.$datasetName;
        }

        return $api->putDatasetRecord($datasetName, $data);
    }

    /**
     * @return mixed
     */
    protected function getCurrentLang() {
        return \Config::get('app.locale');
    }
}