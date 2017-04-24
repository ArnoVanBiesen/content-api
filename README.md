# contentApi
Set up a package to load translation from centralized api

## Basic Installation

  - `composer require famousinteractive/content-api`
  
  - Add `Famousinteractive\ContentApi\ContentApiServiceProvider::class` in serviceProvider in config/app.php
    
  - Publish the config file : `php artisan vendor:publish` 
  
  - Create the project on the translation platform
  
  - Launch the command `php artisan famousContentApi:initialize` to generate the config file
