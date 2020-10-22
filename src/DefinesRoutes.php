<?php

namespace Apsonex\ServotizerCore;

use Illuminate\Support\Facades\Route;

trait DefinesRoutes
{
    /**
     * Ensure that servotizer's internal routes are defined.
     *
     * @return void
     */
    public function ensureRoutesAreDefined()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::post(
            '/servotizer/signed-storage-url',
            Contracts\SignedStorageUrlController::class.'@store'
        )->middleware('web');
    }
}
