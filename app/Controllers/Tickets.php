<?php

namespace App\Controllers;

class Tickets extends BaseController
{
    public function nuevo()
    {
        // Solo cargamos la vista sin guardar en base de datos
        return view('tickets/nuevo');
    }

    public function cola()
    {
        return view('tickets/cola');
    }

    public function misTickets()
    {
        return view('tickets/mis_tickets');
    }

    public function asignados()
    {
        return view('tickets/asignados');
    }
}
