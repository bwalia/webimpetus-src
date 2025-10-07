<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

Class Options implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Use Response object instead of raw header() for CI 4.5+ compatibility
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        
        $method = $request->getMethod();
        if (strtoupper($method) === 'OPTIONS') {
            return $response->setStatusCode(200);
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
      // Do something here
      return $response;
    }
}
?>