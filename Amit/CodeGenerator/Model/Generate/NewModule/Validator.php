<?php
namespace Amit\CodeGenerator\Model\Generate\NewModule;

use Magento\Framework\Exception\LocalizedException;

class Validator implements \Amit\CodeGenerator\Api\ValidatorInterface
{
    /** @var \Magento\Framework\Module\ModuleListInterface */
    protected $moduleListInterface;

    /** @var \Magento\Framework\Module\Dir */
    protected $dir;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Module\ModuleListInterface $moduleListInterface
     * @param \Magento\Framework\Module\Dir $dir
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleListInterface,
        \Magento\Framework\Module\Dir $dir
    ) {
        $this->moduleListInterface = $moduleListInterface;
        $this->dir = $dir;
    }

    /**
     * @var string
     */
    private $validationRule = '/^[a-zA-Z]+[a-zA-Z0-9._]+$/';

    /**
     * Generate Module
     *
     * @param array $data
     * @return array
     */
    public function validate($data)
    {
        $type = $data['type'];
        $module = $data['module'];
        $response = [];
        if (!$type) {
            throw new \InvalidArgumentException(__('Define type of code to be generated "new-module"'));
        }
        if ($module) {
            $moduleData = $this->moduleListInterface->getOne($module);
            if ($moduleData) {
                throw new LocalizedException(
                    __(
                        '%1 Module already exists.',
                        $module
                    )
                );
            }
            $response["module"] = $module;
            $response["type"] = $type;
            
        } else {
            throw new \InvalidArgumentException(__("module name not provided"));
        }
        $moduleNameSplit = explode('_', $module);
        if (!isset($moduleNameSplit[1])) {
            throw new \RuntimeException(
                __('Incorrect module name "%1", correct name ex: Amit_Test', $module)
            );
        }
        
        foreach ($moduleNameSplit as $part) {
            if (!preg_match($this->validationRule, $part)) {
                throw new \RuntimeException(
                    __('Module vendor or name must be alphanumeric.')
                );
            }
        }
        
        return $response;
    }
}
