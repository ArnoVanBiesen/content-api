<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 22/05/2017
 * Time: 16:27
 */

namespace Famousinteractive\ContentApi\Library;
use Illuminate\Support\Facades\Cache;

class Dataset
{

    public static function get($datasetName, $prefixLang=false, $param="all", $preferCache=true) {
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

    public static function put($datasetName, $data = array(), $prefixLang=false) {

        $instance = new self();
        $api = Api::getApi();

        if($prefixLang) {
            $datasetName = $instance->getCurrentLang().'-'.$datasetName;
        }

        return $api->putDatasetRecord($datasetName, $data);
    }

    protected function getCurrentLang() {
        return \Config::get('app.locale');
    }
}