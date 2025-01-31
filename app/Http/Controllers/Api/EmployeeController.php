<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Reports\FileService;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{
    public function __invoke(FileService $excel)
    {
            $filePath = $excel->generateExcel();
            if (Storage::exists($filePath)) {
                return response()->json([
                    'message' => 'File generated successfully!',
                    'file_url' => Storage::url($filePath),
                ], 200, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0',
                ]);
            }
    
            return response()->json(['message' => 'File generation is in progress...'], 202);
    }
}