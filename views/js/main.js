/**
 * m4p_clarity
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

(function () {
    'use strict';

    var cfg = window.m4pClarity;
    if (!cfg || !cfg.m4p_clarity_code) {
        return;
    }

    var M4P_COOKIE = 'm4p_cookies_consent';
    var consent = null;
    var loaded = false;

    /**
     * Consent stored by M4P Cookies, from its script or from the cookie it writes.
     *
     * @return {{analytics: boolean, ads: boolean}|null}
     */
    function m4pCookiesConsent() {
        var categories = null;

        if (window.M4PCookies && window.M4PCookies.consent) {
            categories = window.M4PCookies.consent.c;
        } else {
            var match = document.cookie.match(new RegExp('(?:^|; )' + M4P_COOKIE + '=([^;]*)'));
            if (match) {
                try {
                    categories = (JSON.parse(decodeURIComponent(match[1])) || {}).c;
                } catch (e) {
                    categories = null;
                }
            }
        }

        return categories ? { analytics: !!categories.analytics, ads: !!categories.marketing } : null;
    }

    /**
     * Google Consent Mode commands among dataLayer entries, applied over `base`: gtag() pushes its
     * arguments object, ['consent', 'default' | 'update', {...}], and an update may name one key only.
     *
     * @return {{analytics: boolean, ads: boolean}|null} null when no entry is a consent command
     */
    function consentModeConsent(entries, base) {
        var found = null;

        for (var i = 0; i < entries.length; i++) {
            var entry = entries[i];
            if (!entry || entry[0] !== 'consent' || !entry[2] || typeof entry[2] !== 'object') {
                continue;
            }
            var state = found || base || { analytics: false, ads: false };
            found = {
                analytics: typeof entry[2].analytics_storage === 'string' ? entry[2].analytics_storage === 'granted' : state.analytics,
                ads: typeof entry[2].ad_storage === 'string' ? entry[2].ad_storage === 'granted' : state.ads
            };
        }

        return found;
    }

    function load() {
        loaded = true;
        (function (c, l, a, r, i, t, y) {
            c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
            t = l.createElement(r); t.async = 1; t.src = 'https://www.clarity.ms/tag/' + encodeURIComponent(i);
            y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
        })(window, document, 'clarity', 'script', cfg.m4p_clarity_code);
    }

    function apply() {
        if (!cfg.requireConsent) {
            if (!loaded) {
                load();
            }
            return;
        }

        var granted = !!(consent && consent.analytics);
        if (granted && !loaded) {
            load();
        }
        if (!loaded) {
            return;
        }

        window.clarity('consentv2', {
            ad_Storage: consent && consent.ads ? 'granted' : 'denied',
            analytics_Storage: granted ? 'granted' : 'denied'
        });
        if (!granted) {
            // Microsoft documents the v1 call as the one that erases Clarity's cookies and ends the session.
            window.clarity('consent', false);
        }
    }

    function report(next) {
        if (next) {
            consent = next;
            apply();
        }
    }

    /**
     * Passes the consent of a banner that neither is M4P Cookies nor sends Google Consent Mode.
     *
     * @param {boolean} analytics
     * @param {boolean} [ads]
     */
    cfg.consent = function (analytics, ads) {
        report({ analytics: !!analytics, ads: !!ads });
    };

    document.addEventListener('m4pCookiesConsent', function () {
        report(m4pCookiesConsent());
    });

    var dataLayer = window.dataLayer = window.dataLayer || [];
    var push = dataLayer.push;
    dataLayer.push = function () {
        var result = push.apply(dataLayer, arguments);
        report(consentModeConsent(arguments, consent));

        return result;
    };

    consent = m4pCookiesConsent() || consentModeConsent(dataLayer, null);
    apply();
})();
