<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;

class ExportEmployeesJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;
    public $filePath;
    /**
     * Create a new job instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $directory = 'exports';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        // Log::info('Current Memory: ' . memory_get_usage(true));
        $writer = new Writer();
        $writer->openToFile(Storage::path($this->filePath));
        
        $headerStyle = (new Style())->setFontBold();

        $headerRow = Row::fromValues([
            'Employee No', 'Birth Date', 'First Name', 'Last Name', 'Gender', 'Hire Date'
        ], $headerStyle);

        $writer->addRow($headerRow);
        Employee::query()->chunk(1000, function ($employees) use ($writer) {
            foreach ($employees as $employee) {
                $row = Row::fromValues([
                    $employee->emp_no,
                    Carbon::parse($employee->birth_date)->format('Y-m-d'),
                    $employee->first_name,
                    $employee->last_name,
                    $employee->gender,
                    Carbon::parse($employee->hire_date)->format('Y-m-d'),
                ]);
                $writer->addRow($row);
            }
        });
        $writer->close();
        // Log::info('After Export Memory: ' . memory_get_usage(true));
    }
}