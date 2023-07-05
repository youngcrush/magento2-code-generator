<?php
namespace Amit\CodeGenerator\Model\Generate\Command;

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
     * Validate command params
     *
     * @param array $data
     * @return array
     */
    public function validate($data)
    {
        $module = $data['module'];
        $type = $data['type'];
        $name = $data['name'];

        $response = [];
        if ($module) {
            $moduleData = $this->moduleListInterface->getOne($module);
            if (!$moduleData) {
                throw new \InvalidArgumentException(__("Invalid module name"));
            }
            $response["module"] = $module;
        } else {
            throw new \InvalidArgumentException(__("Module name not provided"));
        }

        if ($name) {
            $response["name"] = $name;
        } else {
            throw new \InvalidArgumentException(__("name is required"));
        }

        if (isset($data['command']) && $data['command']) {
            $response["command"] = $data['command'];
        } else {
            throw new \InvalidArgumentException(__("command is required"));
        }

        $modulePath = $this->dir->getDir($module);
        $response["path"] = $modulePath;
        $response["type"] = $type;
        
        return $response;
    }
}
