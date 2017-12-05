<?php

namespace Famousinteractive\ContentApi\Library;

use Illuminate\Support\Facades\Cache;

/**
 * Class Trans
 * @package Famousinteractive\ContentApi\Library
 */
class Trans
{
    /**
     * @param $key
     * @param array $params
     * @param null $lang
     * @param string $default
     * @param bool $preferCache
     * @return mixed
     */
    public static function get($key, $params = [], $lang=null, $default = '', $preferCache=true) {

        $instance = new self();

        if(is_null($lang)) {
            $lang = $instance->getCurrentLang();
        }

        if($preferCache) {

            $value = Cache::remember('cache-fitrans-' . $key . '-' . $lang, config('famousContentApi.cacheDuration'), function () use ($instance, $key, $default, $lang) {
                return $instance->getTranslation($key, $default, $lang);
            });
        } else {
            $value = $instance->getTranslation($key, $default, $lang);
        }

        return $instance->replaceParameters($value, $params);
    }

    /**
     * @param $type
     * @param $exclusion
     * @param $key
     * @param array $params
     * @param null $lang
     * @param null $default
     * @return bool|string
     */
    public static function getSpecialDisplay($type, $exclusion, $key, $params = [], $lang = null, $default = null) {
        $explodedKey = explode('.', $key);

        if( $type == 'display_keys' &&
            (
                empty($exclusion)
                ||
                ( isset($explodedKey[0]) && !empty($exclusion) && $explodedKey[0] != $exclusion )
            )
        ) {
            return $key;
        }

        if( $type == 'edit_keys' &&
            (
                empty($fitransPrefixExlusion)
                ||
                ( isset($explodedKey[0]) && !empty($exclusion) && $explodedKey[0] != $exclusion )
            )
        ) {
            return '<span class="famous-content-patform-edit-in-page" data-key="'.$key.'">' . self::get($key, $params, $lang, $default, false) . '</span>';
        }

        return false;
    }

    /**
     * @param $key
     * @param $default
     * @param $params
     * @param $lang
     * @return mixed
     */
    protected function getTranslation($key, $default, $lang) {

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
        return $translation;
    }

    /**
     * @return mixed
     */
    protected function getCurrentLang() {
        return \Config::get('app.locale');
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    protected function replaceParameters($value, $params = []) {

        foreach($params as $key=>$v) {
            $value = str_replace(':'.$key, $v, $value);
        }

        return $value;
    }
}
