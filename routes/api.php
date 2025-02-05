<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;

Route::post('/employees/export',[EmployeeController::class,'export'])->name('employees.export');
Route::get('/employees/all',[EmployeeController::class,'allEmployees'])->name('employees.all');
Route::get('/employees/all-from-storage',[EmployeeController::class,'allEmployeesFromStorage'])->name('employees.all-storage');
