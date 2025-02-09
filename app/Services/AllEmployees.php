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
    public function employeesFromStorage($file, int $page = 1)
    {
        if (!file_exists($file)) {
            return response()->json('File not found!');
        }

        $reader = ReaderFactory::createFromFile($file);
        $reader->open($file);

        $data = collect();
        $perPage = 50;
        $totalRows = 0;
        $start = ($page - 1) * $perPage + 1;
        $end = $page * $perPage;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $totalRows++;
                if ($totalRows >= $start && $totalRows <= $end) {
                    $data->push($row->toArray());
                }
                if ($totalRows >= $end) {
                    break 2;
                }
            }
        }
        $reader->close();

        $paginate = new LengthAwarePaginator(
            $data,
            $totalRows,
            $perPage,
            $page,
            ['path' => url()->current()]
        );
        return $paginate;
    }
}
