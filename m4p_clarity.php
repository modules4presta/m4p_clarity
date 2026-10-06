<?php

/**
 * m4p_clarity
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}


class M4p_Clarity extends Module
{
    public function __construct()
    {
        $this->name = 'm4p_clarity';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'Modules4Presta';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Microsoft Clarity', [], 'Modules.M4pclarity.Admin');
        $this->description = $this->trans('Connects the shop to Microsoft Clarity: session recordings and click maps, without touching templates.', [], 'Modules.M4pclarity.Admin');
    }

    const DEFAULTS = [
        'm4p_clarity_text' => '',
        'm4p_clarity_switch' => 0,
    ];

    public function install()
    {
        foreach (self::DEFAULTS as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        return parent::install()
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        Configuration::deleteByName('m4p_clarity_text');
        Configuration::deleteByName('m4p_clarity_switch');

        return parent::uninstall();
    }

    public function displayForm()
    {
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->trans('Settings', [], 'Modules.M4pclarity.Admin'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->trans('Enabled', [], 'Modules.M4pclarity.Admin'),
                    'name' => 'm4p_clarity_switch',
                    'is_bool' => true,
                    'desc' => $this->trans('Turns the Clarity script on and off.', [], 'Modules.M4pclarity.Admin'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('On', [], 'Modules.M4pclarity.Admin'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Off', [], 'Modules.M4pclarity.Admin'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Clarity project ID', [], 'Modules.M4pclarity.Admin'),
                    'name' => 'm4p_clarity_text',
                    'desc' => $this->trans('In Clarity, open My Projects, click the gear icon and copy the project ID.', [], 'Modules.M4pclarity.Admin'),
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Modules.M4pclarity.Admin'),
                'class' => 'btn btn-default pull-right',
            ],
        ];
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->trans('Save', [], 'Modules.M4pclarity.Admin'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->trans('Back to list', [], 'Modules.M4pclarity.Admin'),
            ],
        ];
        $helper->tpl_vars = [
            'fields_value' => [
                'm4p_clarity_text' => Configuration::get('m4p_clarity_text'),
                'm4p_clarity_switch' => Configuration::get('m4p_clarity_switch'),
            ],
            'languages' => $this->context->controller->getLanguages(),
        ];

        return $helper->generateForm($fields_form);
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $projectId = trim((string) Tools::getValue('m4p_clarity_text'));
            $enabled = (int) Tools::getValue('m4p_clarity_switch') ? 1 : 0;

            if (!preg_match('/^[a-z0-9]{5,20}$/i', $projectId)) {
                $output .= $this->displayError($this->trans('That is not a valid Clarity project ID.', [], 'Modules.M4pclarity.Admin'));
            } else {
                Configuration::updateValue('m4p_clarity_text', $projectId);
                Configuration::updateValue('m4p_clarity_switch', $enabled);

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&conf=6');
            }
        }

        return $output . $this->consentNotice() . $this->displayForm();
    }

    /**
     * Clarity records what the visitor does on screen, which EU law treats as
     * analytics requiring prior consent. The shop owner has to be told that
     * before switching the module on, not after a complaint.
     */
    protected function consentNotice()
    {
        return $this->displayWarning(
            $this->trans(
                'Microsoft Clarity records how visitors use your shop: mouse movement, clicks, scrolling and the content of the pages they see.',
                [],
                'Modules.M4pclarity.Admin'
            ) . ' ' . $this->trans(
                'Under EU law (GDPR and the ePrivacy directive) you must tell visitors about this recording before it starts and obtain their consent for analytics cookies.',
                [],
                'Modules.M4pclarity.Admin'
            ) . ' ' . $this->trans(
                'Switch this module on only once your cookie banner reports that analytics consent has been given, and describe the recording in your privacy policy.',
                [],
                'Modules.M4pclarity.Admin'
            )
        );
    }

    public function hookActionFrontControllerSetMedia()
    {
        $projectId = Configuration::get('m4p_clarity_text');

        if ((int) Configuration::get('m4p_clarity_switch') !== 1 || !$projectId) {
            return;
        }

        Media::addJsDef([
            'm4pClarity' => [
                'm4p_clarity_code' => $projectId,
            ],
        ]);

        $this->context->controller->registerJavascript(
            'modules-m4p-clarity',
            'modules/' . $this->name . '/views/js/main.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }
}
