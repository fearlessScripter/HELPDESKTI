<?php
namespace App\Controllers;

class BaseConocimientos extends BaseController
{
    public function index()
    {
        return view('base_conocimientos/index');
    }
}