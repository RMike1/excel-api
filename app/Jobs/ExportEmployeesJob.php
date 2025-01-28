<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

class ExportEmployeesJob implements ShouldQueue
{
    use Queueable;
    public string $filePath;
    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath)
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
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'Employee No');
        $activeWorksheet->setCellValue('B1', 'Birth Date');
        $activeWorksheet->setCellValue('C1', 'First Name');
        $activeWorksheet->setCellValue('D1', 'Last Name');
        $activeWorksheet->setCellValue('E1', 'Gender');
        $activeWorksheet->setCellValue('F1', 'Hire Date');
        $row = 2;
        Employee::chunkById(500, function(Collection $employees) use($activeWorksheet, &$row){
            foreach ($employees as $employee) {
                $activeWorksheet->setCellValue("A$row", $employee->emp_no);
                $activeWorksheet->setCellValue("B$row", Carbon::parse($employee->birth_date)->format('Y-m-d'));
                $activeWorksheet->setCellValue("C$row", $employee->first_name);
                $activeWorksheet->setCellValue("D$row", $employee->last_name);
                $activeWorksheet->setCellValue("E$row", $employee->gender);
                $activeWorksheet->setCellValue("F$row", Carbon::parse($employee->hire_date)->format('Y-m-d'));
                $row++;
            }
        },'emp_no');
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path($this->filePath));
    }
}
