<?php

use GrayLoon\FireChaser\Http\Controllers\FireChaserSyncController;
use Illuminate\Support\Facades\Route;

Route::get('/api/_firechaser/sync', FireChaserSyncController::class)
    ->name('firechaser.sync');
