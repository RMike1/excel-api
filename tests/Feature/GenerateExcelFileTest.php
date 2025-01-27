<?php

use Mockery\MockInterface;
use App\Services\Reports\FileService;

it('generates excel file', function(){
    $spy=$this->spy(FileService::class);
    $this->postJson('api/employees')
    ->assertStatus(200)
    ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    ->assertHeader('Content-Disposition', 'attachment; filename="' . 'employees.xlsx"');  
    $spy->shouldHaveReceived()->generateExcel();
});