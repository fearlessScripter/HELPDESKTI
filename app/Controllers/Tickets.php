<?php

namespace App\Controllers;

class Tickets extends BaseController
{
    private function getSessionData()
    {
        $session = session();

        // Si no está logueado, redirigir al login
        if (!$session->get('isLoggedIn')) {
            return null;
        }

        return [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];
    }

    public function nuevo()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        return view('tickets/nuevo', $data);
    }

    public function cola()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        return view('tickets/cola', $data);
    }

    public function misTickets()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        return view('tickets/mis_tickets', $data);
    }

    public function asignados()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        return view('tickets/asignados', $data);
    }
}
