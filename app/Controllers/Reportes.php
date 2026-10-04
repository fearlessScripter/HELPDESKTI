<?php
namespace App\Controllers;

class Reportes extends BaseController
{
    public function index()
    {
        return view('reportes/index');
    }
}