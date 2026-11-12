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
        $this->version = '1.1.0';
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
        'm4p_clarity_consent' => 1,
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
        foreach (array_keys(self::DEFAULTS) as $key) {
            Configuration::deleteByName($key);
        }

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
                    'type' => 'switch',
                    'label' => $this->trans('Wait for analytics consent', [], 'Modules.M4pclarity.Admin'),
                    'name' => 'm4p_clarity_consent',
                    'is_bool' => true,
                    'desc' => $this->trans('Loads Clarity only after the visitor accepts analytics cookies, and stops the recording when the consent is withdrawn. The consent comes from M4P Cookies, from Google Consent Mode v2 or from a call to m4pClarity.consent().', [], 'Modules.M4pclarity.Admin'),
                    'values' => [
                        [
                            'id' => 'consent_on',
                            'value' => 1,
                            'label' => $this->trans('On', [], 'Modules.M4pclarity.Admin'),
                        ],
                        [
                            'id' => 'consent_off',
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
                'm4p_clarity_consent' => Configuration::get('m4p_clarity_consent'),
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
                Configuration::updateValue('m4p_clarity_consent', (int) Tools::getValue('m4p_clarity_consent') ? 1 : 0);

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&conf=6');
            }
        }

        return $output . $this->consentNotice() . $this->consentSourceNotice() . $this->displayForm();
    }

    /**
     * Without M4P Cookies the tag waits for a signal the module cannot see from the back office,
     * so the page names the two the script understands.
     */
    protected function consentSourceNotice()
    {
        if ((int) Configuration::get('m4p_clarity_consent') !== 1 || Module::isEnabled('m4pcookies')) {
            return '';
        }

        return $this->displayWarning(
            $this->trans(
                'M4P Cookies is not active. Clarity loads once your cookie banner sends Google Consent Mode v2 with analytics_storage granted, or calls m4pClarity.consent(true) in the page.',
                [],
                'Modules.M4pclarity.Admin'
            ) . ' ' . $this->trans(
                'With a banner that does neither, the tag does not load: switch "Wait for analytics consent" off and load the module the way that banner requires.',
                [],
                'Modules.M4pclarity.Admin'
            )
        );
    }

    /**
     * Clarity records what the visitor does on screen, which EU law treats as
     * analytics requiring prior consent, so the page says so while the module
     * loads the tag without waiting for it.
     */
    protected function consentNotice()
    {
        if ((int) Configuration::get('m4p_clarity_consent') === 1) {
            return '';
        }

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
                'Turn on "Wait for analytics consent" or load the module only once your cookie banner reports that consent, and describe the recording in your privacy policy.',
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
                'requireConsent' => (int) Configuration::get('m4p_clarity_consent') === 1,
            ],
        ]);

        $this->context->controller->registerJavascript(
            'modules-m4p-clarity',
            'modules/' . $this->name . '/views/js/main.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }
}
