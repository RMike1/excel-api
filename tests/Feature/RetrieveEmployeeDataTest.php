<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use App\Services\AllEmployees;
use function Pest\Laravel\getJson;

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

it('can even fetch Employees data from storage', function () {
    Storage::fake('local');
    $spy = $this->spy(AllEmployees::class);
    $this->getJson(route('employees.all-storage', ['page' => 1]))
        ->assertStatus(200);
    $spy->shouldHaveReceived()->employeesFromStorage(Mockery::any(), 1)->once();
});
