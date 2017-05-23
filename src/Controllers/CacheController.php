<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 05/04/2017
 * Time: 14:01
 */

namespace Famousinteractive\ContentApi\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CacheController extends Controller
{

    public function clear(Request $request) {

        if($request->get('clientId') == config('famousContentApi.clientId')) {
            \Artisan::call('cache:clear');
            echo 'Done';
        } else {
            echo '0';
        }
    }
}