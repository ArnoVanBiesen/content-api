<?php

namespace Famousinteractive\ContentApi\Library;


use Famousinteractive\Translators\Models\Content;
use Famousinteractive\Translators\Models\ContentTranslation;
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

            $value = Cache::remember('cache-fitrans-' . $key . '-' . $lang.'-params'.$paramsString, 3600, function () use ($instance, $key, $default, $params, $lang) {
                return $instance->getTranslation($key, $default, $params, $lang);
            });
        } else {
            $value = $instance->getTranslation($key, $default, $params, $lang);
        }

        return $value;
    }

    protected function getTranslation($key, $default, $params, $lang) {

        $content = Content::where('key', $key)->first();
        $translation = ContentTranslation::where('content_id', $content->id)->where('lang', $lang)->first();

        if(empty($content)) {
            $content = Content::create(['key' => $key]);
        }

        if(empty($translation)) {
            $translation = ContentTranslation::create([
                'content_id'    => $content->id,
                'lang'          => $lang,
                'value'         => $default
            ]);
        }

        //Generate missing translation for each language

        foreach(config('famousTranslator.lang') as $language) {
            $translationCount = ContentTranslation::where('content_id', $content->id)->where('lang', $language)->count();
            if($translationCount == 0) {
                ContentTranslation::create([
                    'content_id'    => $content->id,
                    'lang'          => $language,
                    'value'         => $default
                ]);
            }
        }

        return $this->replaceParameters($translation->value, $params);
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
