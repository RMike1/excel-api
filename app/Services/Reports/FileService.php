<?php

namespace App\Services\Reports;

use App\Jobs\ExportEmployeesJob;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;



class FileService
{
    /**
     * Create a new class instance.
     */
    public function generateExcel(): string
    {
        $name = now()->format('YmdHis'); 
        $filePath = "exports/employees_{$name}.xlsx";
        
        ExportEmployeesJob::dispatch($filePath)->onQueue('export_excel');
        return $filePath;
    }
}   