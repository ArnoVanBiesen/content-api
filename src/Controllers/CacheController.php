<?php
namespace Famousinteractive\ContentApi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class CacheController
 * @package Famousinteractive\ContentApi\Controllers
 */
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