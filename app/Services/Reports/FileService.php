<?php

namespace App\Services\Reports;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileService
{
    /**
     * Create a new class instance.
     */
    public function excel()
    {
        $employees = Employee::take(10)->get();
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'Employee No');
        $activeWorksheet->setCellValue('B1', 'Birth Date');
        $activeWorksheet->setCellValue('C1', 'First Name');
        $activeWorksheet->setCellValue('D1', 'Last Name');
        $activeWorksheet->setCellValue('E1', 'Gender');
        $activeWorksheet->setCellValue('F1', 'Hire Date');
        $row = 2;
        foreach ($employees as $employee) {
            $activeWorksheet->setCellValue("A$row", $employee->emp_no);
            $activeWorksheet->setCellValue("B$row", Carbon::parse($employee->birth_date)->format('Y-m-d'));
            $activeWorksheet->setCellValue("C$row", $employee->first_name);
            $activeWorksheet->setCellValue("D$row", $employee->last_name);
            $activeWorksheet->setCellValue("E$row", $employee->gender);
            $activeWorksheet->setCellValue("F$row", Carbon::parse($employee->hire_date)->format('Y-m-d'));
            $row++;
        }
        return $spreadsheet;
    }
}
