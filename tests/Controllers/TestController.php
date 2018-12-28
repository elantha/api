<?php

namespace Grizmar\Api\Tests\Controllers;

use Grizmar\Api\Controllers\BaseController;
use Grizmar\Api\Exceptions\ApiException;
use Grizmar\Api\Http\Exceptions\EmptyException;
use Grizmar\Api\Http\Exceptions\ForbiddenException;
use Grizmar\Api\Http\Exceptions\NotFoundException;
use Grizmar\Api\Tests\Errors\CodeRegistry;

class TestController extends BaseController
{
    protected function initValidationRules(): array
    {
        return [
            'testValidationErrors' => [
                'one' => 'required|max:6',
                'two' => 'required',
            ],
            'testValidationRules' => [
                'one'   => 'required',
                'two'   => 'required|alpha_num|size:10',
                'three' => 'required|numeric',
            ],
        ];
    }

    public function testResponseContent()
    {
        $this->output('level1.level2.one', 'value');
        $this->output('level1.level2.two', 100);

        return \response()->rest($this->response);
    }

    public function testExceptionWithResponse()
    {
        $this->response->setData(['info' => ['one' => 'value']]);

        $this->output('info.two', 0.15);

        throw ForbiddenException::make()->setResponse($this->response);
    }

    public function testEmptyException()
    {
        $this->output('param', 'value');

        throw EmptyException::make()->setResponse($this->response);
    }

    public function testExceptionErrors()
    {
        throw NotFoundException::make(CodeRegistry::USER_NOT_FOUND, ['name' => 'Jack']);
    }

    public function testCustomErrors()
    {
        $this->error(CodeRegistry::USER_NOT_FOUND, 'User not found');
        $this->error(100, 'Custom error');

        throw NotFoundException::make()
            ->withoutMessage()
            ->setResponse($this->response);
    }

    public function testValidationErrors()
    {
        throw new ApiException('This method should not be called!');
    }

    public function testValidationRules()
    {
        $this->output('one', $this->input('one'));

        return \response()->rest($this->response);
    }
}
