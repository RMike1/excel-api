<?php

use Mockery\MockInterface;
use App\Jobs\ExportEmployeesJob;
use App\Services\Reports\FileService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

it('generates excel file with FileService', function(){
    $spy=$this->spy(FileService::class);
    $this->postJson(route('employees.export'))
    ->assertStatus(200)
    ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $spy->shouldHaveReceived()->generateExcel();
});

it('exports employees via the endpoint', function () {
    Storage::fake('local');
    Queue::fake();
    $response = $this->postJson(route('employees.export'));
    Queue::assertPushedOn('export_excel', ExportEmployeesJob::class);
    $path = 'private/exports';
    $filePath = $path . '/employees.xlsx';
    Storage::disk('local')->makeDirectory($path);
    (new ExportEmployeesJob($filePath))->handle();
    Storage::assertExists($filePath);
    $response->assertStatus(202)
        ->assertJson([
            'message' => 'File generation is in progress.',
        ]);
});
