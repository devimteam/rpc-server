<?php

namespace Devim\Component\RpcServer\Smd\Exception;

/**
 * Class SmdException
 */
class SmdException extends \Exception
{
    const INVALID_CLASS_NAME = 32500;
    
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     * @param mixed $data
     */
    public function __construct(string $message, int $code = 0, \Exception $previous = null, mixed $data = null)
    {
        parent::__construct($message, $code, $previous);

        $this->data = $data;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
