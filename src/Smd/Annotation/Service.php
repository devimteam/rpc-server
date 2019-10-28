<?php

namespace Devim\Component\RpcServer\Smd\Annotation;

use Devim\Component\RpcServer\Smd\Annotation\Definitions\DefinitionsManager;
use Devim\Component\RpcServer\Smd\Annotation\Errors\ErrorsParser;
use Devim\Component\RpcServer\Smd\Annotation\Parameters\ParametersParser;
use Devim\Component\RpcServer\Smd\Annotation\Returns\ReturnParser;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class Service
{
    public $name = "";
    /**
     * @Required
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $definitions;

    /**
     * @var DefinitionsManager
     */
    public $definitionsManager;
    
    /**
     * @var string
     */
    private $docBlock;
    
    /**
     * @var ParametersParser
     */
    private $parametersParser;

    /**
     * @var ErrorsParser
     */
    private $errorsParser;

    /**
     * @var ReturnParser
     */
    private $returnParser;

    public function __construct()
    {
        $this->parametersParser = new ParametersParser($this);
        $this->errorsParser     = new ErrorsParser($this);
        $this->returnParser     = new ReturnParser($this);
    }

    public function setDocBlock(string $docBlock)
    {
        $this->docBlock = $docBlock;
    }

    public function initDefinitions()
    {
        $definitions = preg_replace('|\s+|', ' ', trim($this->definitions));

        $definitions = str_replace(["\n",'*'], "", $definitions);
        $definitions = str_replace("'", '"', $definitions);

        $this->definitionsManager = new DefinitionsManager($definitions);
    }
    /**
     * @param string $docBlock
     * @param string $name
     * @return array
     */
    public function getSmdInfo(): array
    {
        $parameters = $this->parametersParser->parseDocBlock($this->docBlock);
        $errors = $this->errorsParser->parseDocBlock($this->docBlock);
        $return = $this->returnParser->parseDocBlock($this->docBlock);
        
        $smd_info = [
            'description' => $this->description,
            'parameters' => $parameters->getSmdInfo(),
            'returns' => $return->getSmdInfo(),
            'errors' => $errors->getSmdInfo()
        ];
        
        return $smd_info;
    }
}
