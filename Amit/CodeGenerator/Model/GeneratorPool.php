<?php
namespace Amit\CodeGenerator\Model;

class GeneratorPool
{
    /**
     * @var array
     */
    protected $generators = [];

    /**
     * Construct function
     *
     * @param array $generators
     */
    public function __construct(
        $generators = []
    ) {
        $this->generators = $generators;
    }

    /**
     * Get generator class
     *
     * @param string $key
     * @return Amit\CodeGenerator\Api\GenerateInterface
     */
    public function get($key)
    {
        if (isset($this->generators[$key])) {
            return $this->generators[$key];
        }

        throw new \Magento\Framework\Exception\LocalizedException(__("invalid generator"));
    }
}
