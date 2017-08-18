# contentApi
Set up a package to load translation from centralized api

## Basic Installation

  - `composer require famousinteractive/content-api`
  
  - Add `Famousinteractive\ContentApi\ContentApiServiceProvider::class` in serviceProvider in config/app.php
    
  - Publish the config file : `php artisan vendor:publish` 
  
  - Create the project on the translation platform
  
  - Launch the command `php artisan famousContentApi:initialize` to generate the config file
  
## Usage
  
  - use `fitrans($key, $params = [], $lang = null, $default = '') ` to use the translation via the Content Api.
  
## Dataset

  - To get the data of a dataset, use the function `fitds($datasetName, $prefixLang = false, $param=[], $useCache=true)`
     * `$datasetName` is the unique name of the dataset
     * `$prefixLang`  Boolean allowing you to use a different dataset for each language.
     * `$param`   string with the data to return. Values :
        * "fields" Just the list and the type of the fields. 
        * "datas" All entries in the dataset with information on the fields.
        * "formatted" An array with the association with Field => value and no more data.
        * "All" All these data in once. By default
     * `$useCache` Boolean. True by default. If you get data who change often (like dataset to store user data for example), you may prefer to not cache the result.    

  - To put new record in a dataset, use the function `fitpushds($datasetName, $data=[], $prefixLang = false)`
     * `$datasetName` and `$prefixLang` have the same behavior as for fitds() function
     * `$data` is an array with an association of field name and value. 
        * `[
                'lastname'  => 'Dillenbourg',
                'firstname'     => 'Jérémy',
                'birtdate'         => '1990-07-03'
            ]`
     * The return is either true or an array `['success' => false, 'message' => 'error_description']` . The fields are validated by the rules set in the platform. If the validation fails, you'll receive an error response.
            
## Extra
  
  - You can call `/fit-content-api-clear-cache?clientId=xxxxxx` in order to clear the cache of the website     
  - You can run  `php artisan famousContentApi:collect ` to collect all the fitrans() reference and send them to the platform.    
  - You can force to render all the translation key by adding paremeters to the url of your website 
    * `?fitrans=display_keys&exclusion=routes`
    * fitrans=display_keys render all the keys
    * exlusion exlude some pattern. Here all keys starting by `routes.`. It's usefull if you use fitrans() in the route file.