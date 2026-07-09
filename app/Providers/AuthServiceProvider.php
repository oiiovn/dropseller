<?php

namespace App\Providers;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Commission;
use App\Models\Crm\Customer;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use App\Policies\Crm\AffiliatePolicy;
use App\Policies\Crm\CommissionPolicy;
use App\Policies\Crm\CustomerPolicy;
use App\Policies\Crm\OrderPolicy;
use App\Policies\Crm\PaymentPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Affiliate::class => AffiliatePolicy::class,
        Customer::class => CustomerPolicy::class,
        Order::class => OrderPolicy::class,
        Commission::class => CommissionPolicy::class,
        Payment::class => PaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
