<?php

it('generates excel file')->postJson('api/employees')
    ->assertStatus(200)
    ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    ->assertHeader('Content-Disposition', 'attachment; filename="' . 'employees' . '.xlsx"');