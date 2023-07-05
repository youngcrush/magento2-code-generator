<?php
namespace Amit\CodeGenerator\Model\Generate\Model;

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
     * Generate Model
     *
     * @param array $data
     * @return array
     */
    public function validate($data)
    {
        $module = $data['module'];
        $type = $data['type'];
        $name = $data['name'];
        $table = $data['table']??null;
        $path = $data['path']??null;
        $response = [];
        if ($module) {
            $moduleData = $this->moduleListInterface->getOne($module);
            if (!$moduleData) {
                throw new \InvalidArgumentException(__("Invalid module name"));
            }
            if (!$table) {
                throw new \InvalidArgumentException(__("Please provide table name for generating model"));
            }
            $response["module"] = $module;
            $response["table"] = $table;
            $response["name"] = $name;
        } else {
            throw new \InvalidArgumentException(__("Module name not provided"));
        }

        switch (strtolower($type)) {
            case "model":
                if (!$name) {
                    
                    throw new \InvalidArgumentException(
                        __("Enter model name that need to be generated")
                    );
                }
                $response["type"] = $type;
                break;

            case "controller":
                throw new \InvalidArgumentException(
                    __("Enter controller name that need to be generated")
                );
            default:
                throw new \InvalidArgumentException(__("Define type of code to be generated like model, controller, helper"));
        }
        $modulePath = $this->dir->getDir($module);
        if ($path) {
            $realPath = $modulePath.DIRECTORY_SEPARATOR.$path;
            if (!is_dir($realPath) || !file_exists($realPath)) {
                throw new \InvalidArgumentException(__("invalid module path given: ". $realPath));
            }
            $response["path"] = $realPath;
        } else {
            $response["path"] = $modulePath;
        }
        return $response;
    }
}
