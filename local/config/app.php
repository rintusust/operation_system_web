<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => true,

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'asia/dhaka',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'daily'),

    'log_level' => env('APP_LOG_LEVEL', 'production'),
    /*
        |--------------------------------------------------------------------------
        | Item per page in pagination
        |--------------------------------------------------------------------------
        */
    'item_per_page'=>20,
    'title_lg_HRM'=>["title"=>'Human Resource Management'],
    'title_lg_SD'=>["title"=>'Salary Disbursement'],
    'title_lg_AVURP'=>["title"=>'Ansar VDP Unit Reform Project'],
    'title_lg_recruitment'=>['training'=>["title"=>'Training module(Recruitment)'],"title"=>"Ansar Recruitment"],
    'title_lg_'=>["title"=>'Ansar & VDP ERP'],
    'title_mini_HRM'=>'HRM',
    'title_mini_SD'=>'SD',
    'title_mini_AVURP'=>'AVURP',
//    'title_mini_recruitment'=>'AR',
    'title_mini_'=>'ERP',
    'modules'=>[
        ['name'=>'HRM','route'=>'HRM'],
        ['name'=>'PRSD','route'=>'SD'],
        ['name'=>'ADAPS','route'=>'#'],
        ['name'=>'AVURP','route'=>'AVURP'],
        ['name'=>strtoupper('recruitment'),'route'=>'recruitment'],
        ['name'=>strtoupper('operation'),'route'=>'operation'],

    ],
    //  'offer'=>[
       //  42,18,42,66,67,68,69,62,71,70,72,74,75,23,25,
    // ],

   'offer'=>[
        //42,18,42,66,67,68,69,65,71,70,72,74,75,2,7,8,9,11,12,16,26,31,48,55,102,105
        11,18,42,65,66,67,68,69,70,71,72,74,75
   ],
    'exclude_district'=>[
        9=>[70,71,9,72],
        70=>[70,71,9,72],
        71=>[70,71,9,72],
        72=>[70,71,9,72],
        66=>[66,67,68,69,13,65,74,75],
        67=>[66,67,68,69,13,65,74,75],
        68=>[66,67,68,69,13,65,74,75],
        69=>[66,67,68,69,13,65,74,75],
        65=>[66,67,68,69,13,65,74,75],
        74=>[66,67,68,69,13,65,74,75],
        75=>[66,67,68,69,13,65,74,75],
    ],
    'DG'=>[66,67,68,69,65,74,75],
    'CG'=>[70,71,72],
    'file_export_path'=>storage_path('export_file'),
    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */
    "bank_email"=>"alam.shohag@dutchbanglabank.com",
    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\modules\ModuleServiceProvider::class,
        Barryvdh\Snappy\ServiceProvider::class,
        App\modules\SD\Provider\DemandConstantProvider::class,
        \App\Providers\GlobalParameterProvider::class,
        \App\Providers\LanguageConvertorProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        \Milon\Barcode\BarcodeServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,
        DaveJamesMiller\Breadcrumbs\ServiceProvider::class,
        \Nathanmac\Utilities\Parser\ParserServiceProvider::class,
        \App\Providers\NotificationProvider::class,
        \App\Providers\UserPermissionProvider::class,
        \App\Providers\CustomValidatorProvider::class,
        Sentry\SentryLaravel\SentryLaravelServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
        \App\Providers\RepositoryProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'PDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,
        'PDFImage' => Barryvdh\Snappy\Facades\SnappyImage::class,
        'DC' => App\modules\SD\Helper\Facades\DemandConstantFacdes::class,
        'GlobalParameter' => \App\Helper\Facades\GlobalParameterFacades::class,
        'LanguageConverter' => \App\Helper\Facades\LanguageConverterFacades::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'DNS1D' => \Milon\Barcode\Facades\DNS1DFacade::class,
        'DNS2D' => \Milon\Barcode\Facades\DNS2DFacade::class,
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,
        'Breadcrumbs' => DaveJamesMiller\Breadcrumbs\Facade::class,
        'Notification'=>\App\Helper\Facades\ForgetPasswordFacedes::class,
        'UserPermission'=>\App\Helper\Facades\UserPermissionFacades::class,
        'Sentry' => Sentry\SentryLaravel\SentryFacade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class ,
    ],

];
