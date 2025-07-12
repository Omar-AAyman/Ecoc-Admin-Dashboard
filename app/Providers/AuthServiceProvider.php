<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Tank;
use App\Models\Transaction;
use App\Models\Truck;
use App\Models\User;
use App\Models\Vessel;
use App\Policies\ProductPolicy;
use App\Policies\TankPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\TruckPolicy;
use App\Policies\UserPolicy;
use App\Policies\VesselPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Transaction::class => TransactionPolicy::class,
        Product::class => ProductPolicy::class,
        Vessel::class => VesselPolicy::class,
        User::class => UserPolicy::class,
        Tank::class => TankPolicy::class,
        Truck::class => TruckPolicy::class,
        Trailer::class => TrailerPolicy::class,
        Driver::class => DriverPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
