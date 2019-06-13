<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Devim\Component\RpcServer\Smd\Annotation;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Error
{
    /**
     * @Required
     *
     * @var string
     */
    public $code;

    /**
     * @Required
     *
     * @var string
     */
    public $description;
}
