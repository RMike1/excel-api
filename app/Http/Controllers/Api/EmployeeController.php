<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Reports\FileService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{
    public function __invoke(FileService $excel)
    {
            $spreadsheet = $excel->generateExcel();
            return response()->stream(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . "employees" . ".xlsx" . '"',
                'Cache-Control' => 'max-age=0',
            ]);
    }
}