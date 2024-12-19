<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit043dd16cfbc09f98741d61dc32af3bc2
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\' => 59,
            'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\' => 44,
            'Io\\Prosopo\\Procaptcha\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\' => 
        array (
            0 => __DIR__ . '/..' . '/prosopo/views/private-classes',
        ),
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\' => 
        array (
            0 => __DIR__ . '/..' . '/prosopo/views/src',
        ),
        'Io\\Prosopo\\Procaptcha\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Io\\Prosopo\\Procaptcha\\Assets_Manager' => __DIR__ . '/../..' . '/../src/Assets_Manager.php',
        'Io\\Prosopo\\Procaptcha\\Captcha\\Captcha_Assets' => __DIR__ . '/../..' . '/../src/Captcha/Captcha_Assets.php',
        'Io\\Prosopo\\Procaptcha\\Captcha\\Captcha_Assets_Manager' => __DIR__ . '/../..' . '/../src/Captcha/Captcha_Assets_Manager.php',
        'Io\\Prosopo\\Procaptcha\\Captcha\\Procaptcha' => __DIR__ . '/../..' . '/../src/Captcha/Procaptcha.php',
        'Io\\Prosopo\\Procaptcha\\Captcha\\Widget_Arguments' => __DIR__ . '/../..' . '/../src/Captcha/Widget_Arguments.php',
        'Io\\Prosopo\\Procaptcha\\Collection' => __DIR__ . '/../..' . '/../src/Collection.php',
        'Io\\Prosopo\\Procaptcha\\Integration\\Form\\Form_Helper' => __DIR__ . '/../..' . '/../src/Integration/Form/Form_Helper.php',
        'Io\\Prosopo\\Procaptcha\\Integration\\Form\\Form_Integration' => __DIR__ . '/../..' . '/../src/Integration/Form/Form_Integration.php',
        'Io\\Prosopo\\Procaptcha\\Integration\\Form\\Hookable_Form_Integration' => __DIR__ . '/../..' . '/../src/Integration/Form/Hookable_Form_Integration.php',
        'Io\\Prosopo\\Procaptcha\\Integration\\Plugin\\Plugin_Integration' => __DIR__ . '/../..' . '/../src/Integration/Plugin/Plugin_Integration.php',
        'Io\\Prosopo\\Procaptcha\\Integration\\Plugin\\Plugin_Integrations' => __DIR__ . '/../..' . '/../src/Integration/Plugin/Plugin_Integrations.php',
        'Io\\Prosopo\\Procaptcha\\Integration\\Plugin\\Plugin_Integrator' => __DIR__ . '/../..' . '/../src/Integration/Plugin/Plugin_Integrator.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\BBPress\\BBPress' => __DIR__ . '/../..' . '/../src/Integrations/BBPress/BBPress.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\BBPress\\BBPress_Forum' => __DIR__ . '/../..' . '/../src/Integrations/BBPress/BBPress_Forum.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Contact_Form_7' => __DIR__ . '/../..' . '/../src/Integrations/Contact_Form_7.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Elementor_Pro\\Elementor_Form_Field' => __DIR__ . '/../..' . '/../src/Integrations/Elementor_Pro/Elementor_Form_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Elementor_Pro\\Elementor_Login_Widget' => __DIR__ . '/../..' . '/../src/Integrations/Elementor_Pro/Elementor_Login_Widget.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Elementor_Pro\\Elementor_Pro' => __DIR__ . '/../..' . '/../src/Integrations/Elementor_Pro/Elementor_Pro.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Everest_Forms\\Everest_Forms' => __DIR__ . '/../..' . '/../src/Integrations/Everest_Forms/Everest_Forms.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Everest_Forms\\Everest_Forms_Field' => __DIR__ . '/../..' . '/../src/Integrations/Everest_Forms/Everest_Forms_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Fluent_Forms\\Fluent_Forms' => __DIR__ . '/../..' . '/../src/Integrations/Fluent_Forms/Fluent_Forms.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Fluent_Forms\\Fluent_Forms_Field' => __DIR__ . '/../..' . '/../src/Integrations/Fluent_Forms/Fluent_Forms_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Formidable_Forms\\Formidable_Form_Field' => __DIR__ . '/../..' . '/../src/Integrations/Formidable_Forms/Formidable_Form_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Formidable_Forms\\Formidable_Forms' => __DIR__ . '/../..' . '/../src/Integrations/Formidable_Forms/Formidable_Forms.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Gravity_Forms\\Gravity_Form_Field' => __DIR__ . '/../..' . '/../src/Integrations/Gravity_Forms/Gravity_Form_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Gravity_Forms\\Gravity_Forms' => __DIR__ . '/../..' . '/../src/Integrations/Gravity_Forms/Gravity_Forms.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\JetPack\\JetPack' => __DIR__ . '/../..' . '/../src/Integrations/JetPack/JetPack.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\JetPack\\JetPack_Form_Field' => __DIR__ . '/../..' . '/../src/Integrations/JetPack/JetPack_Form_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Ninja_Forms\\Ninja_Form_Field' => __DIR__ . '/../..' . '/../src/Integrations/Ninja_Forms/Ninja_Form_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Ninja_Forms\\Ninja_Forms' => __DIR__ . '/../..' . '/../src/Integrations/Ninja_Forms/Ninja_Forms.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Spectra\\Spectra' => __DIR__ . '/../..' . '/../src/Integrations/Spectra/Spectra.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\Spectra\\Spectra_Form_Block_Field' => __DIR__ . '/../..' . '/../src/Integrations/Spectra/Spectra_Form_Block_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\User_Registration\\UR_Login_Form' => __DIR__ . '/../..' . '/../src/Integrations/User_Registration/UR_Login_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\User_Registration\\UR_Lost_Password_Form' => __DIR__ . '/../..' . '/../src/Integrations/User_Registration/UR_Lost_Password_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\User_Registration\\User_Registration' => __DIR__ . '/../..' . '/../src/Integrations/User_Registration/User_Registration.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WPForms\\WPForms' => __DIR__ . '/../..' . '/../src/Integrations/WPForms/WPForms.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WPForms\\WPForms_Field' => __DIR__ . '/../..' . '/../src/Integrations/WPForms/WPForms_Field.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WooCommerce\\WooCommerce' => __DIR__ . '/../..' . '/../src/Integrations/WooCommerce/WooCommerce.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WooCommerce\\Woo_Checkout_Form' => __DIR__ . '/../..' . '/../src/Integrations/WooCommerce/Woo_Checkout_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WooCommerce\\Woo_Login_Form' => __DIR__ . '/../..' . '/../src/Integrations/WooCommerce/Woo_Login_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WooCommerce\\Woo_Lost_Password_Form' => __DIR__ . '/../..' . '/../src/Integrations/WooCommerce/Woo_Lost_Password_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WooCommerce\\Woo_Order_Tracking_Form' => __DIR__ . '/../..' . '/../src/Integrations/WooCommerce/Woo_Order_Tracking_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WooCommerce\\Woo_Register_Form' => __DIR__ . '/../..' . '/../src/Integrations/WooCommerce/Woo_Register_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\Comment_Form' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/Comment_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\Login_Form' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/Login_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\Lost_Password_Form' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/Lost_Password_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\Password_Protected_Form' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/Password_Protected_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\Register_Form' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/Register_Form.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\Shortcode' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/Shortcode.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\WordPress' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/WordPress.php',
        'Io\\Prosopo\\Procaptcha\\Integrations\\WordPress\\WordPress_Form' => __DIR__ . '/../..' . '/../src/Integrations/WordPress/WordPress_Form.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Assets_Manager_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Assets_Manager_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Captcha\\Captcha_Assets_Manager_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Captcha/Captcha_Assets_Manager_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Captcha\\Captcha_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Captcha/Captcha_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Hooks_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Hooks_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Integration\\Form\\Form_Helper_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Integration/Form/Form_Helper_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Integration\\Form\\Form_Integration_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Integration/Form/Form_Integration_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Integration\\Form\\Hookable_Form_Integration_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Integration/Form/Hookable_Form_Integration_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Integration\\Plugin\\Plugin_Integration_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Integration/Plugin/Plugin_Integration_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Settings\\Settings_Storage_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Settings/Settings_Storage_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Interfaces\\Settings\\Settings_Tab_Interface' => __DIR__ . '/../..' . '/../src/Interfaces/Settings/Settings_Tab_Interface.php',
        'Io\\Prosopo\\Procaptcha\\Plugin' => __DIR__ . '/../..' . '/../src/Plugin.php',
        'Io\\Prosopo\\Procaptcha\\Query_Arguments' => __DIR__ . '/../..' . '/../src/Query_Arguments.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Settings_Page' => __DIR__ . '/../..' . '/../src/Settings/Settings_Page.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Settings_Storage' => __DIR__ . '/../..' . '/../src/Settings/Settings_Storage.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Settings_Tab' => __DIR__ . '/../..' . '/../src/Settings/Settings_Tab.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Tabs\\Account_Forms_Settings' => __DIR__ . '/../..' . '/../src/Settings/Tabs/Account_Forms_Settings.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Tabs\\General_Settings' => __DIR__ . '/../..' . '/../src/Settings/Tabs/General_Settings.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Tabs\\Statistics' => __DIR__ . '/../..' . '/../src/Settings/Tabs/Statistics.php',
        'Io\\Prosopo\\Procaptcha\\Settings\\Tabs\\Woo_Commerce_Settings' => __DIR__ . '/../..' . '/../src/Settings/Tabs/Woo_Commerce_Settings.php',
        'Io\\Prosopo\\Procaptcha\\Template_Models\\Settings\\Settings_Form_Model' => __DIR__ . '/../..' . '/../src/Template_Models/Settings/Settings_Form_Model.php',
        'Io\\Prosopo\\Procaptcha\\Template_Models\\Settings\\Settings_General_Tab_Model' => __DIR__ . '/../..' . '/../src/Template_Models/Settings/Settings_General_Tab_Model.php',
        'Io\\Prosopo\\Procaptcha\\Template_Models\\Settings\\Settings_Model' => __DIR__ . '/../..' . '/../src/Template_Models/Settings/Settings_Model.php',
        'Io\\Prosopo\\Procaptcha\\Template_Models\\Settings\\Settings_Statistics_Model' => __DIR__ . '/../..' . '/../src/Template_Models/Settings/Settings_Statistics_Model.php',
        'Io\\Prosopo\\Procaptcha\\Template_Models\\Widget_Model' => __DIR__ . '/../..' . '/../src/Template_Models/Widget_Model.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\BaseTemplateModel' => __DIR__ . '/..' . '/prosopo/views/src/BaseTemplateModel.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\CodeRunnerInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/CodeRunnerInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\EventDispatcherInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/EventDispatcherInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Model\\ModelFactoryInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Model/ModelFactoryInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Model\\ModelNameResolverInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Model/ModelNameResolverInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Model\\ModelNamespaceResolverInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Model/ModelNamespaceResolverInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Model\\ModelRendererInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Model/ModelRendererInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Model\\TemplateModelInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Model/TemplateModelInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Model\\TemplateModelWithDefaultsInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Model/TemplateModelWithDefaultsInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Object\\ObjectPropertyWriterInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Object/ObjectPropertyWriterInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Object\\ObjectReaderInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Object/ObjectReaderInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Object\\PropertyValueProviderInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Object/PropertyValueProviderInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Template\\ModelTemplateResolverInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Template/ModelTemplateResolverInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Template\\TemplateCompilerInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Template/TemplateCompilerInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\Template\\TemplateRendererInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/Template/TemplateRendererInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\View\\ViewNamespaceManagerInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/View/ViewNamespaceManagerInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\Interfaces\\View\\ViewNamespaceModulesContainerInterface' => __DIR__ . '/..' . '/prosopo/views/src/Interfaces/View/ViewNamespaceModulesContainerInterface.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Blade\\BladeCompiler' => __DIR__ . '/..' . '/prosopo/views/private-classes/Blade/BladeCompiler.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\CodeRunner\\CodeRunnerWithErrorHandler' => __DIR__ . '/..' . '/prosopo/views/private-classes/CodeRunner/CodeRunnerWithErrorHandler.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\CodeRunner\\CodeRunnerWithGlobalArguments' => __DIR__ . '/..' . '/prosopo/views/private-classes/CodeRunner/CodeRunnerWithGlobalArguments.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\CodeRunner\\CodeRunnerWithTemplateCompilation' => __DIR__ . '/..' . '/prosopo/views/private-classes/CodeRunner/CodeRunnerWithTemplateCompilation.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\CodeRunner\\PhpCodeRunner' => __DIR__ . '/..' . '/prosopo/views/private-classes/CodeRunner/PhpCodeRunner.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\EventDispatcher' => __DIR__ . '/..' . '/prosopo/views/private-classes/EventDispatcher.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelFactory' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelFactory.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelFactoryWithDefaultsManagement' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelFactoryWithDefaultsManagement.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelFactoryWithSetupCallback' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelFactoryWithSetupCallback.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelNameResolver' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelNameResolver.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelNamespaceResolver' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelNamespaceResolver.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelRenderer' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelRenderer.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Model\\ModelRendererWithEventDetails' => __DIR__ . '/..' . '/prosopo/views/private-classes/Model/ModelRendererWithEventDetails.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\ObjectClassReader' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/ObjectClassReader.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\ObjectPropertyWriter' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/ObjectPropertyWriter.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\ObjectReader' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/ObjectReader.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\PropertyValueProvider' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/PropertyValueProvider.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\PropertyValueProviderByTypes' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/PropertyValueProviderByTypes.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\PropertyValueProviderForModels' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/PropertyValueProviderForModels.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Object\\PropertyValueProviderForNullable' => __DIR__ . '/..' . '/prosopo/views/private-classes/Object/PropertyValueProviderForNullable.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Template\\FileModelTemplateResolver' => __DIR__ . '/..' . '/prosopo/views/private-classes/Template/FileModelTemplateResolver.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Template\\TemplateRenderer' => __DIR__ . '/..' . '/prosopo/views/private-classes/Template/TemplateRenderer.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Template\\TemplateRendererWithCustomEscape' => __DIR__ . '/..' . '/prosopo/views/private-classes/Template/TemplateRendererWithCustomEscape.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Template\\TemplateRendererWithEventDetails' => __DIR__ . '/..' . '/prosopo/views/private-classes/Template/TemplateRendererWithEventDetails.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Template\\TemplateRendererWithFileTemplate' => __DIR__ . '/..' . '/prosopo/views/private-classes/Template/TemplateRendererWithFileTemplate.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\Template\\TemplateRendererWithModelsRender' => __DIR__ . '/..' . '/prosopo/views/private-classes/Template/TemplateRendererWithModelsRender.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\View\\ViewNamespace' => __DIR__ . '/..' . '/prosopo/views/private-classes/View/ViewNamespace.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\PrivateClasses\\View\\ViewNamespaceModulesContainer' => __DIR__ . '/..' . '/prosopo/views/private-classes/View/ViewNamespaceModulesContainer.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\View\\ViewNamespaceConfig' => __DIR__ . '/..' . '/prosopo/views/src/View/ViewNamespaceConfig.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\View\\ViewNamespaceModules' => __DIR__ . '/..' . '/prosopo/views/src/View/ViewNamespaceModules.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\View\\ViewTemplateRenderer' => __DIR__ . '/..' . '/prosopo/views/src/View/ViewTemplateRenderer.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\View\\ViewTemplateRendererConfig' => __DIR__ . '/..' . '/prosopo/views/src/View/ViewTemplateRendererConfig.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\View\\ViewTemplateRendererModules' => __DIR__ . '/..' . '/prosopo/views/src/View/ViewTemplateRendererModules.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\ViewsManager' => __DIR__ . '/..' . '/prosopo/views/src/ViewsManager.php',
        'Io\\Prosopo\\Procaptcha\\Vendors\\Prosopo\\Views\\ViewsManagerConfig' => __DIR__ . '/..' . '/prosopo/views/src/ViewsManagerConfig.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit043dd16cfbc09f98741d61dc32af3bc2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit043dd16cfbc09f98741d61dc32af3bc2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit043dd16cfbc09f98741d61dc32af3bc2::$classMap;

        }, null, ClassLoader::class);
    }
}