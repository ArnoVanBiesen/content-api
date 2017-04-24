<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 05/04/2017
 * Time: 14:01
 */

namespace Famousinteractive\ContentApi\Library;


class Api
{
    protected static $_instance = null;

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

    public function getBy($key, $language) {

    }


    public function pushMissingTranslation() {

    }


}