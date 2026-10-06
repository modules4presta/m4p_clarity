# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
