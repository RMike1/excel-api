<?php

use App\Models\Employee;
use Mockery\MockInterface;
use Illuminate\Support\Str;
use App\Jobs\ExportEmployeesJob;
use App\Services\Reports\FileService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

it('generates excel file', function(){
    $spy=$this->spy(FileService::class);
    $this->postJson(route('employees.export'))
    ->assertStatus(200)
    ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $spy->shouldHaveReceived()->generateExcel();
});

it('dispatches export job and returns response', function () {
    Queue::fake();
    Storage::fake('local');
    $this->postJson(route('employees.export'))
    ->assertStatus(202)
    ->assertJson([
            'message' => 'File generation is in progress...',
    ]);
    Queue::assertPushed(ExportEmployeesJob::class, function ($job) {
        return Str::startsWith($job->filePath, 'exports/employees_');
    });
});