<?php
//https://www.twilio.com/blog/create-secured-restful-api-codeigniter-php
namespace App\Controllers;

use App\Models\Core\Common_model;
use App\Models\Users_model;
use App\Models\Contact;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Auth extends BaseController
{
    /**
     * Register a new user
     * @return Response
     * @throws ReflectionException
     */
    /* public function register()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[user.email]',
            'password' => 'required|min_length[8]|max_length[255]'
        ];

 $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

        $userModel = new Users_model();
       $userModel->save($input);
     

       

return $this
            ->getJWTForUser(
                $input['email'],
                ResponseInterface::HTTP_CREATED
            );

    } */

    /**
     * Authenticate Existing User
     * @return Response
     */
    public function login()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[3]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];

        $input = $this->getRequestInput($this->request);


        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        return $this->getJWTForUser($input['email'], $input['password'], "contacts");
    }

    public function user_login()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[3]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];

        $input = $this->getRequestInput($this->request);


        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        return $this->getJWTForUser($input['email'], $input['password'], "users");
    }
    public function employee_login()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[3]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        return $this->getJWTForUser($input['email'], $input['password'], "employees");
    }

    private function getJWTForUser(
        string $emailAddress,
        string $password,
        string $loginType,
        int $responseCode = ResponseInterface::HTTP_OK
    ) {
        try {
            $model = new Common_model();
            $user = $model->findByEmailAddress($loginType, $emailAddress);
            if ($user['password'] != md5($password)) {
                return $this
                    ->getResponse(
                        [
                            'error' => 'Password Not match',
                        ],
                        ResponseInterface::HTTP_FORBIDDEN
                    );
            }
            if ($loginType == "contacts") {
                if ($user['allow_web_access'] != '1') {
                    return $this
                        ->getResponse(
                            [
                                'error' => 'User do do have access to webpage!',
                            ],
                            ResponseInterface::HTTP_FORBIDDEN
                        );
                }
            }

            unset($user['password']);

            helper('jwt');

            return $this
                ->getResponse(
                    [
                        'message' => 'User authenticated successfully',
                        'user' => $user,
                        'access_token' => getSignedJWTForUser($emailAddress)
                    ],
                    $responseCode
                );
        } catch (Exception $exception) {
            return $this
                ->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    ResponseInterface::HTTP_FORBIDDEN
                );
        }
    }
}
