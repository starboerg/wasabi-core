<?php

namespace Wasabi\Core\Authentication\Exception;

abstract class AuthenticationException extends \Exception
{
    const ERROR_TYPE_WARNING = 'warning';
    const ERROR_TYPE_ERROR = 'error';

    const ERROR_CODE_TOO_MANY_FAILED_LOGIN_ATTEMPTS = 1;
    const ERROR_CODE_INVALID_CREDENTIALS = 2;
    const ERROR_CODE_EMAIL_NOT_VERIFIED = 3;
    const ERROR_CODE_ACCOUNT_NOT_ACTIVATED = 4;
    const ERROR_CODE_ACCOUNT_DISABLED = 5;

    /**
     * Holds the error type.
     * One of 'warning', 'error'.
     *
     * @var string
     */
    protected $errorType;

    /**
     * @var int|null
     */
    protected $errorCode = null;

    /**
     * Get the error type of the exception.
     *
     * @return string
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * Get the error code of the exception.
     *
     * @return int|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
