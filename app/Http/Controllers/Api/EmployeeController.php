<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\AllEmployees;
use App\Http\Controllers\Controller;
use App\Services\Reports\FileService;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function export(FileService $excel)
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

    public function allEmployees(AllEmployees $allEmployees)
    {
        return response()->json([
            $allEmployees->employees(),
        ]);
    }
    public function allEmployeesFromStorage(Request $request, AllEmployees $allEmployeesFromStorage)
    {
        $file = Storage::disk('local')->path('exports/employees_20250205131505.xlsx');
        $page = $request->query('page', 1);
        $perPage = 50;
        
        $employees= $allEmployeesFromStorage->employeesFromStorage($file, $perPage, $page);

        return response()->json($employees);
}

}