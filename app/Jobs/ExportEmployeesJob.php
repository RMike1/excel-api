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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
    // public function handle()
    // {
    //     $directory = 'exports';
    //     if (!Storage::exists($directory)) {
    //         Storage::makeDirectory($directory);
    //     }
    
    //     $tempFilePath = Storage::path($this->filePath);
    //     $path = fopen($tempFilePath, 'w');

    //     $spreadsheet = new Spreadsheet();
    //     $activeWorksheet = $spreadsheet->getActiveSheet();
    //     $activeWorksheet->setCellValue('A1', 'Employee No');
    //     $activeWorksheet->setCellValue('B1', 'Birth Date');
    //     $activeWorksheet->setCellValue('C1', 'First Name');
    //     $activeWorksheet->setCellValue('D1', 'Last Name');
    //     $activeWorksheet->setCellValue('E1', 'Gender');
    //     $activeWorksheet->setCellValue('F1', 'Hire Date');
    //     $row = 2;
    //     Log::info('Current Memory: ' . memory_get_usage(true));
    //     Employee::lazyById(1000, 'emp_no')
    //        ->each (function($employee) use($activeWorksheet, &$row){
    //             $activeWorksheet->setCellValue("A$row", $employee->emp_no);
    //             $activeWorksheet->setCellValue("B$row", Carbon::parse($employee->birth_date)->format('Y-m-d'));
    //             $activeWorksheet->setCellValue("C$row", $employee->first_name);
    //             $activeWorksheet->setCellValue("D$row", $employee->last_name);
    //             $activeWorksheet->setCellValue("E$row", $employee->gender);
    //             $activeWorksheet->setCellValue("F$row", Carbon::parse(time: $employee->hire_date)->format('Y-m-d'));
    //             $row++;
    //             Log::info('inside the loop: ' . memory_get_usage(true));
    //         });
    //         Log::info('After Chunk: ' . memory_get_usage(true));
    //         fclose($path);
            
    //     // });
    //     // $writer = new Xlsx($spreadsheet);
    //     // $writer->save(Storage::path($this->filePath));
    // }
    public function handle()
    {
        $directory = 'exports';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        $tempFilePath = Storage::path($this->filePath);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['Employee No', 'Birth Date', 'First Name', 'Last Name', 'Gender', 'Hire Date'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1) . '1', $header);
        }
        $row = 2;
        Employee::lazyById(2000, 'emp_no')->each(function ($employee) use ($sheet, &$row) {
            $sheet->setCellValue("A$row", $employee->emp_no);
            $sheet->setCellValue("B$row", Carbon::parse($employee->birth_date)->format('Y-m-d'));
            $sheet->setCellValue("C$row", $employee->first_name);
            $sheet->setCellValue("D$row", $employee->last_name);
            $sheet->setCellValue("E$row", $employee->gender);
            $sheet->setCellValue("F$row", Carbon::parse($employee->hire_date)->format('Y-m-d'));
            $row++;

            if ($row % 500 === 0) {
                gc_collect_cycles();
            }
        });
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();
    }
}
