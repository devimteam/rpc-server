<?php

namespace Devim\Component\RpcServer\Smd;

interface SmdGeneratorInterface {
    public function run(\Generator $serviceAnnotationGenerator);
}