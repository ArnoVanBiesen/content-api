<?php
namespace Famousinteractive\ContentApi\Controllers;

use App\Http\Controllers\Controller;
use Famousinteractive\ContentApi\Library\Api;
use Illuminate\Http\Request;

/**
 * Class CacheController
 * @package Famousinteractive\ContentApi\Controllers
 */
class CacheController extends Controller
{
    public function clear(Request $request) {

        if(true) {
            \Artisan::call('cache:clear');

            //We reload the translation automatically

            $api = Api::getApi();
            $allKey = $api->getAll();

            foreach($allKey as $value) {

                foreach($value['translations'] as $trans) {
                    \Cache::remember('cache-fitrans-' . $value['key'] . '-' . $trans['lang'], config('famousContentApi.cacheDuration'), function () use ($trans) {
                        return $trans['value'];
                    });
                }
            }

            echo 'Done';
        } else {
            echo '0';
        }
    }
}