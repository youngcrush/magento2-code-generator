<?php
namespace Amit\CodeGenerator\Model;

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\DocBlock\Tag;

class Helper
{
    /**
     * Save File
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    public function saveFile($path, $content)
    {
        file_put_contents(
            $path,
            $content
        );
    }

    /**
     * Get Header
     *
     * @param string $moduleName
     * @return void
     */
    public function getHeadDocBlock($moduleName)
    {
        return DocBlockGenerator::fromArray([]);
    }

    /**
     * Map valid database types
     *
     * @param string $type
     * @return string
     */
    public function getReturnType($type = 'default')
    {
        $validTypes = [
            'varchar' => 'string',
            'text' => 'string',
            'smallint' => 'int',
            'int' => 'int',
            'integer' => 'int',
            'decimal' => 'float',
            'boolean' => 'bool'
        ];
        return isset($validTypes[$type]) ? $validTypes[$type] : 'string';
    }

    /**
     * Create Diretory
     *
     * @param string $dirPath
     * @param integer $permission
     * @return void
     */
    public function createDirectory($dirPath, $permission = 0777)
    {
        if (!is_dir($dirPath)) {
            mkdir($dirPath, $permission, true);
        }
    }

    /**
     * Generate Template Files
     *
     * @param mixed $template
     * @return array|string
     */
    public function getTemplatesFiles($template)
    {
        return file_get_contents(dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR. $template);
    }

    /**
     * Load Template File
     *
     * @param string $path
     * @param string $fileName
     * @param string $templatePath
     * @param array $replace
     * @return string
     */
    public function loadTemplateFile($path, $fileName, $templatePath, $replace = [])
    {
        $filePath = $path.DIRECTORY_SEPARATOR.$fileName;
        if (!file_exists($filePath)) {
            $data = $this->getTemplatesFiles($templatePath);
            if (!empty($replace) && is_array($replace)) {
                foreach ($replace as $find => $value) {
                    $data = str_replace("%{$find}%", $value, $data);
                }
            }
            $this->saveFile($filePath, $data);
        }
        return $filePath;
    }

    /**
     * Get Di.xml file
     *
     * @param string $etcDirPath
     * @param array $data
     * @return string
     */
    public function getDiXmlFile($etcDirPath, $data)
    {
        $replace = [
            "module_name" => $data['module']
        ];
        return $this->loadTemplateFile($etcDirPath, 'di.xml', 'templates/di.xml.dist', $replace);
    }
    
    /**
     * Get class name
     *
     * @param string $name
     * @return string
     */
    public function getClassName($name)
    {
        $camelCase = '';
        $fields = explode('_', $name);
        $className = ucfirst($name);
        if (count($fields) > 1) {
            $className = '';
            foreach ($fields as $key => $f) {
                if ($key == 0) {
                    $camelCase = ucfirst($f);
                } else {
                    $camelCase.= ucfirst($f);
                }
            }
            $className = $camelCase;
        }
        return $className;
    }
}
