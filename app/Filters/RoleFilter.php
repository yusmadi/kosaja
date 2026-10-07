<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role');

        if (! empty($arguments)) {
            // Check if user's role matches any of the allowed roles in arguments
            if (! in_array($userRole, $arguments, true)) {
                // If user is super_admin, grant full access
                if ($userRole === 'super_admin') {
                    return;
                }

                return redirect()->to('/')->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
