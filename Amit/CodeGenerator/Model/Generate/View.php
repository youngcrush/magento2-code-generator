<?php
namespace Amit\CodeGenerator\Model\Generate;

use Amit\CodeGenerator\Model\Helper;
use Amit\CodeGenerator\Api\GenerateInterface;
use Amit\CodeGenerator\Model\XmlGeneratorFactory;
use Magento\Framework\Simplexml\Config;

/**
 * Generate View
 */
class View implements GenerateInterface
{
    /**
     * @var Helper
     */
    protected $helper;
    
    /**
     * @var XmlGeneratorFactory
     */
    protected $xmlGenerator;

    /**
     * Constructor
     *
     * @param XmlGeneratorFactory $xmlGeneratorFactory
     * @param Helper $helper
     */
    public function __construct(
        XmlGeneratorFactory $xmlGeneratorFactory,
        Helper $helper
    ) {
        $this->helper = $helper;
        $this->xmlGenerator = $xmlGeneratorFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function execute($data)
    {
        $moduleName = $data['module'];
        $path = $data['path'];
        $this->helper->createDirectory(
            $layoutPath = $path.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$data['area'].DIRECTORY_SEPARATOR.
                'layout'
        );
        $block = $this->createBlock($path, $data);
        $phtml = $this->createPhtml($path, $block, $data);
        $this->addLayoutXmlData($layoutPath, $block, $data);
        return ['status' => 'success', 'message' => "View Generated Successfully"];
    }

    /**
     * Create Block class
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    public function createBlock($path, $data)
    {
        $moduleNamespace = explode('_', $data['module']);
        $block = $data['block'];
        $area = $data['area'];
        $childPaths = explode('/', $block);
        $blockClass = array_pop($childPaths);
        if (!empty($childPaths)) {
            $childPaths = array_map(['\Amit\CodeGenerator\Model\Generate\View', 'getClassName'], $childPaths);
            $childPaths = implode(DIRECTORY_SEPARATOR, $childPaths);
        } else {
            $childPaths = '';
        }
        $blockPaths = 'Block';
        if ($area == 'adminhtml') {
            $blockPaths = $blockPaths.DIRECTORY_SEPARATOR.ucfirst($area);
        }
        if ($childPaths) {
            $blockPaths = $blockPaths.DIRECTORY_SEPARATOR.$childPaths;
        }
        $this->helper->createDirectory($path.DIRECTORY_SEPARATOR.$blockPaths);

        $namespace = $moduleNamespace[0].'\\'.$moduleNamespace[1].'\\'.str_replace('/', '\\', $blockPaths);
        $className = $this->helper->getClassName($blockClass);
        $blockFile = $this->helper->getTemplatesFiles('templates/block/block.php.dist');
        $blockFile = str_replace('%class%', $className, $blockFile);
        $blockFile = str_replace(
            '%namespace%',
            $namespace,
            $blockFile
        );
        $blockFile = str_replace(
            '%module_name%',
            $data['module'],
            $blockFile
        );
        $this->helper->saveFile(
            $path.DIRECTORY_SEPARATOR.$blockPaths.DIRECTORY_SEPARATOR.$className.'.php',
            $blockFile
        );
        return $namespace.'\\'.$className;
    }

    /**
     * Create .phtml file
     *
     * @param string $path
     * @param string $block
     * @param array $data
     * @return void
     */
    public function createPhtml($path, $block, $data)
    {
        $templateFileName = $data['phtml'];
        $area = $data['area'];

        $this->helper->createDirectory(
            $templatePath = $path.DIRECTORY_SEPARATOR.
            'view'.DIRECTORY_SEPARATOR.
            $area.DIRECTORY_SEPARATOR.'templates'
        );

        $templateFile = $this->helper->getTemplatesFiles('templates/block/deafult.phtml.dist');
        $templateFile = str_replace('%block%', $block, $templateFile);
        $templateFile = str_replace('%module_name%', $data['module'], $templateFile);

        $this->helper->saveFile(
            $templatePath.DIRECTORY_SEPARATOR.$templateFileName,
            $templateFile
        );
    }

    /**
     * Create class name
     *
     * @param string $string
     * @return string
     */
    public static function getClassName($string)
    {
        $fields = explode('_', $string);
        $camelCase = '';
        $className = ucfirst($string);
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

    /**
     * Add di xml data
     *
     * @param string $layoutPath
     * @param mixed $block
     * @param array $data
     * @return void
     */
    public function addLayoutXmlData($layoutPath, $block, $data)
    {
        $layoutType = $data['layout'];
        $templateFile = $data['phtml'];
        $replace = [
            "module_name" => $data['module']
        ];
        $xmlFile = $this->helper->loadTemplateFile(
            $layoutPath,
            $data['name'].'.xml',
            'templates/layout.xml.dist',
            $replace
        );
        $xmlObj = new Config($xmlFile);

        //remove layout attribute
        $result = $xmlObj->getNode()->xpath("//page/@layout");
        foreach ($result as $node) {
            unset($node[0]);
        }
        
        $layoutXml = $xmlObj->getNode();
        if ($data['area'] != 'adminhtml') {
            // add layout attribute
            if ($layoutType && $layoutType != '1column') {
                $xmlObj->getNode()->addAttribute('layout', $layoutType);
            }
        }
        
        $body = $this->xmlGenerator->addXmlNode($layoutXml, 'body');
        $referenceContainer = $this->xmlGenerator->addXmlNode(
            $body,
            'referenceContainer',
            '',
            ['name' => 'content'],
            'name'
        );

        $this->xmlGenerator->addXmlNode(
            $referenceContainer,
            'block',
            '',
            ['template' => $templateFile, 'class' => $block, 'name' => $data['name']],
            'name'
        );
        $xmlData = $this->xmlGenerator->formatXml($layoutXml->asXml());
        $this->helper->saveFile($xmlFile, $xmlData);
    }
}
