<?php

namespace App\Services;

use App\Models\Employee;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;


class AllEmployees
{
    /**
     * Create a new class instance.
     */
        public function employees()
        {
            $perPage = 50;
            $page=request()->get('page',1);
            $lastId = request()->query('last_id', 0); 
            $employees = Employee::where('emp_no', '>', $lastId)
                                ->orderBy('emp_no')
                                ->take($perPage)
                                ->get();
            $lastEmployee = $employees->last();
            $nextlast_Id = $lastEmployee ? $lastEmployee->emp_no : null;
            $totalRows = Employee::count();
            $paginate = new LengthAwarePaginator(
                $employees,
                $totalRows,
                $perPage,
                $page,
                ['path' => url()->current()]
            );
            $paginate->appends(['last_id' => $nextlast_Id]);
            return $paginate;
        }

        // public function employees()
        // {
        //     return Employee::orderBy('emp_no')->cursorPaginate(100);
        // }
     
    //     public function employees()
    //     {
    //         $perPage = 100;
    //         $page = request()->query('page',1); 
    //         $start = ($page - 1) * $perPage;
    //         $employees = Employee::skip($start)->take($perPage)->get();
    //         $totalRows = Employee::count();
    //         $paginate = new LengthAwarePaginator(
    //             $employees,
    //             $totalRows,
    //             $perPage,
    //             $page,
    //             ['path' => url()->current()]
    //         );
    //     return $paginate;
    // }
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
