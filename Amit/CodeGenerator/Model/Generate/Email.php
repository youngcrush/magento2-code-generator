<?php

namespace Amit\CodeGenerator\Model\Generate;

use Amit\CodeGenerator\Model\Helper;
use Amit\CodeGenerator\Api\GenerateInterface;
use Amit\CodeGenerator\Model\XmlGeneratorFactory;
use Magento\Framework\Simplexml\Config;

/**
 * Generate Email Template
 */
class Email implements GenerateInterface
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
            $emailDirPath = $path.DIRECTORY_SEPARATOR.'view/frontend/email'
        );
        
        $this->helper->createDirectory(
            $etcDirPath = $path.DIRECTORY_SEPARATOR.'etc'
        );
        
        $this->createEmailTemplate($emailDirPath, $data);
        $this->addEmailXmlData($etcDirPath, $data);
       
        return ['status' => 'success', 'message' => "Email Template Generated Successfully"];
    }

    /**
     * Create email template
     *
     * @param string $dir
     * @param array $data
     * @return void
     */
    public function createEmailTemplate($dir, $data)
    {
        $emailFile = $this->helper->getTemplatesFiles('templates/email/email.html.dist');
        $emailFile = str_replace('%module_name%', $data['module'], $emailFile);
        
        $this->helper->saveFile(
            $dir.DIRECTORY_SEPARATOR.$data['template'].'.html',
            $emailFile
        );
    }

    /**
     * Add email_templates.xml data
     *
     * @param string $etcDirPath
     * @param array $data
     * @return void
     */
    public function addEmailXmlData($etcDirPath, $data)
    {
        $replace = [
            "module_name" => $data['module'],
        ];
        $emailXmlFile = $this->helper->loadTemplateFile(
            $etcDirPath,
            'email_templates.xml',
            'templates/email/email_templates.xml.dist',
            $replace
        );
        $xmlObj = new Config($emailXmlFile);
        $configXml = $xmlObj->getNode();
        $this->xmlGenerator->addXmlNode(
            $configXml,
            'template',
            '',
            [
                'id'=>$data['id'],
                'label'=>$data['name'],
                'file'=>$data['template'].'.html',
                'type'=>'html',
                'area'=>'frontend',
                'module'=>$data['module']
            ]
        );
        $xmlData = $this->xmlGenerator->formatXml($configXml->asXml());
        $this->helper->saveFile($emailXmlFile, $xmlData);
    }
}
