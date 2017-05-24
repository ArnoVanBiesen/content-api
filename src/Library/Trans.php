<?php

namespace Famousinteractive\ContentApi\Library;

use Illuminate\Support\Facades\Cache;

class Trans
{
    public static function get($key, $params = [], $lang=null, $default = '', $preferCache=true) {
        $instance = new self();

        if(is_null($lang)) {
            $lang = $instance->getCurrentLang();
        }

        if($preferCache) {

            $paramsString = md5(json_encode($params));

            $value = Cache::remember('cache-fitrans-' . $key . '-' . $lang.'-params'.$paramsString, config('famousContentApi.cacheDuration'), function () use ($instance, $key, $default, $params, $lang) {
                return $instance->getTranslation($key, $default, $params, $lang);
            });
        } else {
            $value = $instance->getTranslation($key, $default, $params, $lang);
        }

        return $value;
    }

    protected function getTranslation($key, $default, $params, $lang) {

        $api = Api::getApi();

        $translation = $api->getBy($key, $lang);

        if(empty($translation)) {
            $api->pushMissingTranslation($key, $lang, $default);
            $translation = $default;
        }

        //Generate missing translation for each language

        if(config('famousContentApi.autoRegister')) {
            foreach (config('famousContentApi.lang') as $language) {

                $translationCount = $api->getBy($key, $language);
                if (empty($translationCount)) {
                    $api->pushMissingTranslation($key, $language, $default);
                }
            }
        }

        return $this->replaceParameters($translation, $params);
    }

    protected function getCurrentLang() {
        return \Config::get('app.locale');
    }

    protected function replaceParameters($value, $params = []) {

        foreach($params as $key=>$v) {
            $value = str_replace(':'.$key, $v, $value);
        }

        return $value;
    }
}
