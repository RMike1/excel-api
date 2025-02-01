<?php

use App\Jobs\ExportEmployeesJob;
use App\Services\Reports\FileService;
use Illuminate\Support\Facades\Queue;

it('dispatches Job with correct file path', function () {
    Queue::fake();
    $fileService = new FileService();
    $filePath = $fileService->generateExcel();
    $this->assertStringStartsWith('exports/employees_', $filePath);
    $this->assertStringEndsWith('.xlsx', $filePath);
    Queue::assertPushed(ExportEmployeesJob::class, function ($job) use ($filePath) {
        return $job->filePath === $filePath;
    });
});
