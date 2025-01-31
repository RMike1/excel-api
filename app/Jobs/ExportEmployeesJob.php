<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

class ExportEmployeesJob implements ShouldQueue
{
    use Queueable;
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
    
        $tempFilePath = Storage::path($this->filePath);
        $path = fopen($tempFilePath, 'w');

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'Employee No');
        $activeWorksheet->setCellValue('B1', 'Birth Date');
        $activeWorksheet->setCellValue('C1', 'First Name');
        $activeWorksheet->setCellValue('D1', 'Last Name');
        $activeWorksheet->setCellValue('E1', 'Gender');
        $activeWorksheet->setCellValue('F1', 'Hire Date');
        $row = 2;
        Log::info('Current Memory: ' . memory_get_usage(true));
        Employee::lazyById(1000, 'emp_no')
           ->each (function($employee) use($activeWorksheet, &$row){
                $activeWorksheet->setCellValue("A$row", $employee->emp_no);
                $activeWorksheet->setCellValue("B$row", Carbon::parse($employee->birth_date)->format('Y-m-d'));
                $activeWorksheet->setCellValue("C$row", $employee->first_name);
                $activeWorksheet->setCellValue("D$row", $employee->last_name);
                $activeWorksheet->setCellValue("E$row", $employee->gender);
                $activeWorksheet->setCellValue("F$row", Carbon::parse(time: $employee->hire_date)->format('Y-m-d'));
                $row++;
                Log::info('inside the loop: ' . memory_get_usage(true));
            });
            Log::info('After Chunk: ' . memory_get_usage(true));
            fclose($path);
        // });
        // $writer = new Xlsx($spreadsheet);
        // $writer->save(Storage::path($this->filePath));
    }
}
