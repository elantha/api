<?php

namespace Grizmar\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Grizmar\Api\Response\ContentInterface;
use Grizmar\Api\Validators\RequestValidator;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, RequestValidator;

    protected $response;
    protected $request;

    final public function __construct(Request $request, ContentInterface $response)
    {
        $this->response = $response;
        $this->request = $request;

        $this->initializeValidationRules();

        // TODO: перенести в обработчик ошибок
        try{
            $this->validate($request, $this->validationRules);
        } catch(ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();

            foreach($errors as $fieldName => $fieldMessages)
            {
                $this->response->setValidationErrors($fieldName, $fieldMessages);
            }

            $this->response->setStatusCode($e->status);
        }
    }

    final protected function hasErrors(): bool
    {
        return $this->response->isValid();
    }

    final protected function input($key, $default = null)
    {
        return $this->request->input($key, $default);
    }

    // TODO: подумать как задавать общие для всех контроллеров правила валидации
    protected function initializeValidationRules(): self
    {
        $this->validationRules = [

        ];

        return $this;
    }
}
