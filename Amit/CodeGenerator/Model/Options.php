<?php
namespace Amit\CodeGenerator\Model;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Options
{
    public const MODULE = 'module';

    public function getOptions()
    {
        $options = [
            /* Add options */
            new InputArgument(
                self::MODULE,
                InputArgument::OPTIONAL,
                'Provide your module name like Vendor_ModuleName'
            ),

            new InputOption(
                'type',
                't',
                InputArgument::OPTIONAL,
                'Define type of code to be generated like model, controller, helper'
            ),
            new InputOption(
                'path',
                'p',
                InputArgument::OPTIONAL,
                'Provide relative path to your module folder to generate code'
            ),
            new InputOption(
                'name',
                'name',
                InputArgument::OPTIONAL,
                'Enter model name or class name that need to be generated'
            ),

            /* Generate specific model */
            new InputOption(
                'table',
                'ta',
                InputArgument::OPTIONAL,
                'Enter name for the model only required for model generation'
            ),

            /* Generate specific repository */
            new InputOption(
                'model-class',
                'mc',
                InputArgument::OPTIONAL,
                'Enter model class with namespace'
            ),

            new InputOption(
                'collection-class',
                'cc',
                InputArgument::OPTIONAL,
                'Enter collection class with namespace'
            ),
            
            /* Generate specific shipping method */
            new InputOption(
                'shipping-code',
                'shipping-code',
                InputArgument::OPTIONAL,
                'Enter shipping method code.'
            ),
          
            /* Generate specific Controller */
            new InputOption(
                'area',
                'ar',
                InputArgument::OPTIONAL,
                'Enter area frontend or adminhtml'
            ),

            new InputOption(
                'router',
                'r',
                InputArgument::OPTIONAL,
                'Enter router name'
            ),

            new InputOption(
                'resource',
                're',
                InputArgument::OPTIONAL,
                'Enter resource name for admin user authorization'
            ),

            /* Generate specific plugin */
            new InputOption(
                'plugin',
                'plugin',
                InputArgument::OPTIONAL,
                'Enter plugin type class'
            ),

            /* Generate specific observer */
            new InputOption(
                'event',
                'event',
                InputArgument::OPTIONAL,
                'Enter event name'
            ),

            /* Generate payment method */
            new InputOption(
                'payment-code',
                'payment-code',
                InputArgument::OPTIONAL,
                'Enter payment method code.'
            ),

            /* Generate cron */
            new InputOption(
                'schedule',
                'schedule',
                InputArgument::OPTIONAL,
                'enter schedule'
            ),

            /* Generate Create view */
            new InputOption(
                'block-class',
                'bc',
                InputArgument::OPTIONAL,
                'Enter block class name'
            ),
            new InputOption(
                'template',
                'template',
                InputArgument::OPTIONAL,
                'Enter phtml template file name'
            ),
            new InputOption(
                'layout-type',
                'lt',
                InputArgument::OPTIONAL,
                'Enter layout type like 1column'
            ),

            /* Generate specific command */ 
            new InputOption(
                'command',
                'command',
                InputArgument::OPTIONAL,
                'Enter command'
            ),

            /* Generate rewrite */
            new InputOption(
                'rewrite',
                'rewrite',
                InputArgument::OPTIONAL,
                'Enter class to be overridden'
            ),

            new InputOption(
                'id',
                'id',
                InputArgument::OPTIONAL,
                'Enter identifier'
            ),

            /* Model Class Name for generate ui listing */
            new InputOption(
                'model_class_name',
                'model_class_name',
                InputArgument::OPTIONAL,
                'Enter model class name for generate ui component'
            ),

            new InputOption(
                'columns_name',
                'columns_name',
                InputArgument::OPTIONAL,
                'Enter columns name for generate ui component grid column'
            ),

            /* Add Vendor Name */
            new InputOption(
                'vendor_name',
                'vendor_name',
                InputArgument::OPTIONAL,
                'Enter vendor name for generate module'
            ),

            /* Add Module Name */
            new InputOption(
                'module_name',
                'module_name',
                InputArgument::OPTIONAL,
                'Enter module name for generate module'
            ),

            /* Add Module Name */
            new InputOption(
            'provider_name',
            'provider_name',
            InputArgument::OPTIONAL,
            'Enter Data Provider name for Ui component form'
            ),

            /* Add Fieldset Name */
            new InputOption(
                'fieldset_name',
                'fieldset_name',
                InputArgument::OPTIONAL,
                'Enter Fieldset name for Ui component form'
            ),

            /* Add Fieldset Label */
            new InputOption(
                'fieldset_label',
                'fieldset_label',
                InputArgument::OPTIONAL,
                'Enter Fieldset Label for Ui component form'
            ),

            /* Add Fieldset Label */
            new InputOption(
                'field_label',
                'field_label',
                InputArgument::OPTIONAL,
                'Enter Field Label for Ui component form'
            ),

            /* Add Fieldset Label */
            new InputOption(
                'field_name',
                'field_name',
                InputArgument::OPTIONAL,
                'Enter Field Label for Ui component form'
            ),

            /* Add Fieldset Label */
            new InputOption(
                'field_type',
                'field_type',
                InputArgument::OPTIONAL,
                'Enter Field Type for Ui component form'
            ),

            /* Add Fieldset Label */
            new InputOption(
                'enter_new_field',
                'enter_new_field',
                InputArgument::OPTIONAL,
                'Enter New Field for Ui component form'
            ),
            /* Add Fieldset Label */
            new InputOption(
                'form_field',
                'form_field',
                InputArgument::OPTIONAL,
                'Add New Field for Ui component form'
            ),

            /* Add Fieldset Label */
            new InputOption(
                'is_required',
                'is_required',
                InputArgument::OPTIONAL,
                'Add New Field is Required or Not for Ui component form'
            ),

            /* Add Form Url */
            new InputOption(
                'submit_url',
                'submit_url',
                InputArgument::OPTIONAL,
                'Submit Url for Ui component form'
            ),

            /* Add Form Image Upload Url */
            new InputOption(
                'image_upload_url',
                'image_upload_url',
                InputArgument::OPTIONAL,
                'Image Upload Url for Ui component form'
            )
        ];
        return $options;
    }
}