<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class JWTAuthenticationFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        // echo '<pre>'; print_r($_SERVER); echo '</pre>'; die;
        
        $pos = strpos($_SERVER['REQUEST_URI'], "api/sendEmail") ?? false;
        $ping = strpos($_SERVER['REQUEST_URI'], "api/v1/ping") ?? false;
        $enquiry = strpos($_SERVER['REQUEST_URI'], "api/v2/enquiries") ?? false;
        $method = $_SERVER['REQUEST_METHOD'];

        if ($pos  ===  false && $ping  ===  false && ($enquiry === false || $method === "POST")) {

            $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');
            try {
                helper('jwt');
                $encodedToken = getJWTFromRequest($authenticationHeader);
                validateJWTFromRequest($encodedToken);
                return $request;
            } catch (Exception $e) {
                return Services::response()
                    ->setJSON(
                        [
                            'error' => $e->getMessage()
                        ]
                    )
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
    }
}
