<?php

namespace App\Providers;

use App\Repositories\Auth\AuthRepository;
use App\Repositories\Auth\AuthRepositoryImplement;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryImplement;
use App\Services\SMS\SMSService;
use App\Services\SMS\TextlocalSMSServiceImplement;
use App\Services\SMS\TwilioSMSServiceImplement;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            UserRepository::class,
            UserRepositoryImplement::class
        );

        $this->app->bind(
            AuthRepository::class,
            AuthRepositoryImplement::class
        );

        $this->app->bind(SMSService::class, function () {
            if (config('services.sms') == 'twilio') {
                return new TwilioSMSServiceImplement();
            } elseif (config('services.sms') == 'textlocal') {
                return new TextlocalSMSServiceImplement();
            } else {
                return null;
            }
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public
    function boot()
    {
        //
    }
}
