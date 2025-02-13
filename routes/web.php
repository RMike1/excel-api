<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;

Route::get('/web/employees',EmployeeController::class)->name('web.employees');
