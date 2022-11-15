<?php

namespace App\core\exception;

// estende la classe Exception di php
class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
}
