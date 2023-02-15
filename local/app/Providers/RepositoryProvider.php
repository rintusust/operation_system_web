<?php

namespace App\Providers;

use App\modules\AVURP\Repositories\VDPInfo\OperationVDPInfoInterface;
use App\modules\AVURP\Repositories\VDPInfo\VDPInfoRepository;
use App\modules\HRM\Repositories\data\DataInterface;
use App\modules\HRM\Repositories\data\DataRepository;
use App\modules\operation\Repositories\data\OperationDataInterface;
use App\modules\operation\Repositories\data\OperationDataRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(
            OperationVDPInfoInterface::class,
            VDPInfoRepository::class
        );
        $this->app->bind(
            DataInterface::class,
            DataRepository::class
        );

        $this->app->bind(
            OperationDataInterface::class,
            OperationDataRepository::class
        );
    }
}
