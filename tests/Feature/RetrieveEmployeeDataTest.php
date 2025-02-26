<?php

use App\Models\Employee;
use App\Services\AllEmployees;
use OpenSpout\Common\Entity\Row;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Writer\XLSX\Writer;
use function Pest\Laravel\getJson;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenSpout\Reader\Common\Creator\ReaderFactory;

it('returns Employees data with pagination', function () {
    $spy = $this->spy(AllEmployees::class);
    $employees = Employee::paginate(20);
    $spy->shouldReceive('employees')
        ->andReturn($employees);
    $this->getJson(route('employees.all'))
        ->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    $spy->shouldHaveReceived()->employees();
});

// it('can even fetch Employees data from storage', function () {
//     Storage::fake();
//     $spy = $this->spy(AllEmployees::class);
//     $this->getJson(route('employees.all-storage'), ['page' => 1])
//         ->assertOk();
//     $spy->shouldHaveReceived()->employeesFromStorage(Mockery::any(), 1)->once();
// });

it('can even fetch Employees data from storage2', function () {
    Storage::fake('local');
    Storage::disk('local')->makeDirectory('exports');
    $filePath = 'employees_123.xlsx';
    $fullPath = Storage::disk('local')->path('/exports'.$filePath);
    $writer = new Writer();
    $writer->openToFile($fullPath);
    $rows = [
        ['emp_no', 'first_name', 'last_name'],
        [1, 'John', 'Doe'],
        [2, 'Jon', 'Snow'],
    ];
    foreach ($rows as $row) {
        $cells = [];
        foreach ($row as $cell) {
            $cells[] = Cell::fromValue($cell);
        }
        $row = new Row($cells);
        $writer->addRow($row);
    }
    $writer->close();
    // $this->assertTrue(Storage::disk('local')->exists($filePath));
    // $file = Storage::disk('local')->path($filePath);
    
    // ====changes========
    $file = new UploadedFile($fullPath, $filePath, 'application/xlsx');


    $allEmployeeService = new AllEmployees();
    $allRows = getAllRowsForFile($file);
    $data = $allEmployeeService->employeesFromStorage($file, 1);
    // dd($data);
    $this->assertInstanceOf(LengthAwarePaginator::class, $data);
    $this->assertEquals(50, $data->perPage());
    $this->assertEquals(1, $data->currentPage());
    $this->assertEquals(count($rows), $data->total());
    $this->assertEquals('emp_no', $data->items()[0][0]);
    $this->assertEquals('first_name', $data->items()[0][1]);
    $this->assertEquals('John', $data->items()[1][1]);
    $this->assertEquals('Doe', $data->items()[1][2]);
});

function getAllRowsForFile(string $file): array
{
    $allRows = [];
    $reader = ReaderFactory::createFromFile($file);
    $reader->open($file);
    foreach ($reader->getSheetIterator() as $sheet) {
        foreach ($sheet->getRowIterator() as $row) {
            $allRows[] = $row->toArray();
        }
    }
    $reader->close();
    return $allRows;
}
