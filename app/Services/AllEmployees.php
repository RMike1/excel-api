<?php

namespace App\Services;

use App\Models\Employee;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use Illuminate\Pagination\LengthAwarePaginator;


class AllEmployees
{
    /**
     * Create a new class instance.
     */
    public function employees()
    {
        return Employee::paginate(20);
    }
    public function employeesFromStorage($file, int $perPage = 50, int $page = 1)
    {
        if (!file_exists($file)) {
            return response()->json('File not found!');
        }

        $reader = ReaderFactory::createFromFile($file);
        $reader->open($file);
        $data = []; 
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $data[] = $row->toArray();
            }
        }
        $reader->close();

        $collection = collect($data);
        $paginate = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => url()->current()]
        );
        return $paginate;
    }

    
}
