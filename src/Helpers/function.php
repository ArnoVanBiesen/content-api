<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 18/04/2017
 * Time: 15:44
 */

if (! function_exists('fitrans')) {
    function fitrans($key, $params = [], $lang = null, $default = '')
    {

        if(config('famousContentApi.useApi')) {
            return \Famousinteractive\ContentApi\Library\Trans::get($key, $params, $lang, $default, config('famousContentApi.useCache', true));
        } else {
            return trans($key, $params);
        }
    }
}


if (! function_exists('fitds')) {
    function fitds($datasetName, $prefixLang = false) {
        if(config('famousContentApi.useApi')) {
            return \Famousinteractive\ContentApi\Library\Dataset::get($datasetName, $prefixLang, config('famousContentApi.useCache', true));
        } else {
            return '';
        }
    }
}