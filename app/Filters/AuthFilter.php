<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Verifica si el usuario tiene sesión activa
     * Si no tiene sesión, redirige al login
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Rutas que NO requieren autenticación
        $publicRoutes = [
            '/',
            '/authenticate',
            '/logout',
            '/diagnostico',
            '/crear-sedes-prueba',
            '/mostrar-sedes',
            '/crear-usuario-prueba',
        ];
        
        // Normalizar la ruta (eliminar barra final si existe y no es la raíz)
        $path = rtrim($path, '/') ?: '/';
        
        // Si la ruta es pública, permitir el acceso sin verificar sesión
        if (in_array($path, $publicRoutes)) {
            return;
        }
        
        $session = session();
        
        // Verificar si el usuario tiene sesión activa
        if (!$session->get('logged_in')) {
            // Si es una petición AJAX, devolver JSON
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.',
                        'redirect' => base_url()
                    ]);
            }
            
            // Para peticiones normales, redirigir al login
            return redirect()->to(base_url())
                ->with('error', 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
        }
    }

    /**
     * No se ejecuta nada después de la petición
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere acción después de la petición
    }
}

