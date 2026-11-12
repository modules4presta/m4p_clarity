# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-11-12

### Added

- A "Wait for analytics consent" setting: the Clarity tag loads only after the visitor accepts
  analytics cookies, and every change of consent is passed to Clarity's `consentv2` API. A withdrawn
  analytics consent also erases Clarity's cookies and ends the session.
- Three consent sources: M4P Cookies, Google Consent Mode v2 (`analytics_storage`, `ad_storage` in
  `dataLayer`) and `m4pClarity.consent(analytics, ads)` for any other banner.
- A notice on the configuration page while consent waiting is on and M4P Cookies is not active.
- An upgrade script that turns consent waiting on where M4P Cookies is active and leaves it off
  elsewhere. New installations start with it on.

### Changed

- The consent notice on the configuration page is shown only while consent waiting is off.

## [1.0.0] - 2026-10-06

First release under the MIT license. The module continues the work previously published as
`m4p_msclarityfree`, rebuilt in a fresh repository, and carries these changes over its last
proprietary version:

### Added

- A notice on the configuration page stating that Clarity records what visitors do on screen, that
  EU law requires telling them before the recording starts and obtaining consent for analytics
  cookies, and that the module should stay off until the consent banner reports that consent.
- Default settings written on install, so a fresh installation starts switched off with no project
  ID rather than with empty values.
- English and Polish translation catalogues, LICENSE, composer.json, an English config manifest and
  the standard documentation set.

### Removed

- A cURL call to modules4presta.io made on every visit to the module configuration, which fetched
  advertisements to display in the back office of somebody else's shop, along with the advertisement
  panel and the two settings that cached its responses.
- A server requirements panel checking for the ionCube Loader, an encoder runtime this module never
  needed.

### Changed

- Compatibility declared against the installed PrestaShop instead of stopping at 8.1.99.
- Author headers no longer carry the retired `kontakt@nice-code.eu` address.
