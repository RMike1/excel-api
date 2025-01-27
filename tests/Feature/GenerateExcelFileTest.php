<?php

use Mockery\MockInterface;
use App\Services\Reports\FileService;

it('generates excel file', function(){
    $spy=$this->spy(FileService::class);
    $this->postJson(route('employees.export'))
    ->assertStatus(200)
    ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $spy->shouldHaveReceived()->generateExcel();
});