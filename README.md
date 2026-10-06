# M4P Microsoft Clarity for PrestaShop 8 & 9

**See where customers hesitate — session recordings and click maps from Microsoft Clarity, connected with one field and no template edits.**

> **Meta description (150 chars):** Connect Microsoft Clarity to PrestaShop: paste the project ID and get session recordings and click maps. No template edits. Free MIT module.

---

## Why watch instead of guess

Analytics tells you that people leave the checkout. It does not tell you that the delivery field
rejects a postcode with a space in it. Clarity records the session, so you watch it happen:

- **Rage clicks and dead clicks** — Clarity flags them, you find the broken button
- **Scroll maps per page** — see whether anyone reaches what you put at the bottom
- **Free, with no sampling** — Microsoft does not charge for Clarity
- **One field to connect** — the project ID, nothing else to configure

## What the module does

You paste the Clarity project ID and switch the module on. It then loads the Clarity tag on the
front office, on every page, with no changes to your theme. Switch it off and the tag is gone.

### Key features

- **One setting** — the project ID, validated before it is saved
- **On and off switch** — stop collecting without uninstalling
- **No template edits** — the script is registered through the asset hook, not pasted into a `.tpl`
- **Nothing else loaded** — the module ships no other third-party code
- **Says so plainly** — the configuration page states the consent obligation before you switch it on

### What it does not do

The module does not ask for consent and does not hold back the tag until a visitor agrees to
analytics cookies. If your shop serves the EU, pair it with your consent banner and enable the
module only when analytics consent has been given — see the question below.

## Compatibility

| | |
|---|---|
| PrestaShop | 1.7.6 – 9.x |
| PHP | 7.2.5+ |
| Requirements | a Microsoft Clarity account and a project ID |
| Multistore | The project ID is shared across shops |
| Themes | Works with any theme — nothing is rendered, only a script registered |

The module performs no core overrides and adds no database tables — it stores two settings.

## Installation

1. Upload and install the module from **Modules → Module Manager**.
2. In Clarity, open **My Projects**, click the gear icon and copy the project ID.
3. Paste it into the module configuration, switch the module on and save.
4. Open the shop, click around, and check that the session appears in Clarity after a few minutes.

## Configuration options

| Setting | Description |
|---|---|
| **Enabled** | Loads the Clarity tag. Off means nothing is sent. |
| **Clarity project ID** | From the Clarity panel; accepted as 5 to 20 letters and digits. |

## Frequently asked questions

**Does this module handle cookie consent?**
No. It loads the tag whenever it is enabled. In the EU, Clarity needs analytics consent, so keep the
module switched off until your consent banner reports that consent, or wire the switch to it. A
module that silently loads a tracker is not something you want to ship to customers in the EU.

**Is Clarity free?**
Yes. Microsoft offers it without charge and without traffic sampling. That is why it is worth
connecting even on a small shop.

**Does it slow the shop down?**
The tag is loaded asynchronously, so it does not block rendering. It is still a third-party request;
measure before and after if page speed is a concern.

**Where is the data stored?**
On Microsoft's servers, under your Clarity account. This module only loads their script; it stores
nothing itself and sends nothing anywhere else.

**What happens to the settings when I uninstall the module?**
Both are deleted.

---

**Keywords:** PrestaShop Microsoft Clarity, session recordings, heatmaps, click maps, analytics,
user behaviour.

## History

This module continues the work published earlier as `m4p_msclarityfree`. It lives in a fresh
repository, so its history starts here.

## License

MIT — see [LICENSE](LICENSE). Free to use commercially, fork and modify; keep the copyright notice.

## Contributing

Bug reports and pull requests are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). For security
issues, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

---

Built by [Nice Code](https://nice-code.com/pl/oferta/moduly-prestashop) — we build and maintain PrestaShop stores.

© Nice Code sp. z o.o. (Modules4Presta) — released under the MIT license.
