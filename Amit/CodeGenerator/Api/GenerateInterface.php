<?php
namespace Amit\CodeGenerator\Api;

interface GenerateInterface
{

    /**
     * Generate code
     *
     * @param array $data
     * @return boolean
     */
    public function execute($data);
}