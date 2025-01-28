<?php

use App\Jobs\ExportEmployeesJob;
use Illuminate\Support\Facades\Storage;

it('processes job n generates excel file', function () {
    Storage::fake('local');
    $path = 'private/exports';
    $filePath = $path . '/employees.xlsx';
    Storage::disk('local')->makeDirectory($path);
    (new ExportEmployeesJob($filePath))->handle();
    Storage::assertExists($filePath);
});