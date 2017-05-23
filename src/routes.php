<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 05/04/2017
 * Time: 14:06
 */


Route::get('/fit-content-api-clear-cache', function() {
    Artisan::call('cache:clear');
});
