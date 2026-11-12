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

/**
 * @param M4p_Clarity $module
 *
 * @return bool
 */
function upgrade_module_1_1_0($module)
{
    if (Configuration::hasKey('m4p_clarity_consent')) {
        return true;
    }

    // Waiting needs a consent source; with a banner other than M4P Cookies the tag keeps loading as before.
    return Configuration::updateValue('m4p_clarity_consent', Module::isEnabled('m4pcookies') ? 1 : 0);
}
