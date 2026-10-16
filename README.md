# KD Dark Light Toggle

**A free dark mode / light mode switch for the Kadence theme (WordPress).**
One click and your whole site flips between two of your own Kadence colour palettes. No coding needed.

![License: GPL v2 or later](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)
![WordPress 5.7+](https://img.shields.io/badge/WordPress-5.7%2B-21759b)
![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-777bb4)
![Theme: Kadence](https://img.shields.io/badge/theme-Kadence-orange)

Keywords: wordpress dark mode, kadence dark mode, kadence dark light toggle, kadence global palette switcher, wordpress dark mode plugin, dark mode switch, light mode switch, night mode wordpress, free dark mode plugin.

---

## What does it do?

If you use the **Kadence theme**, you already have *Global Palettes* (sets of colours for your site).
This plugin lets you say:

- "Palette 1 is my **light** look"
- "Palette 2 is my **dark** look"

Then your visitors get a small switch (sun / moon). They tap it, and the site changes colour right away.

## Why use it?

- **Made for Kadence.** It uses the colours you already set in Kadence. No new colours to learn.
- **Auto dark mode (optional).** If a visitor's phone or computer is in dark mode, your site can start dark too.
- **Remembers the visitor's choice.** It is saved only in their own browser. No cookies, nothing is sent to your server.
- **Fast and light.** One small file, a tiny bit of CSS and JavaScript. No extra libraries.
- **Cache friendly.** Every visitor gets the same page, so caching plugins and Hostinger caching work fine.
- **Easy to put anywhere.** Floating button, shortcode, or widget.
- **Free for everyone.** Use it on personal, client or business sites. Open source (GPL).

## How to install

**Option A: upload the zip**
1. Go to **Plugins → Add New → Upload Plugin**.
2. Choose the zip file and click **Install Now**.
3. Click **Activate**.

**Option B: from GitHub**
1. Open the **Releases** page and download `kd-dark-light-toggle.zip` (not the green Code > Download ZIP button).
2. Upload it the same way as Option A.

> Important: the folder inside the zip must always be called exactly `kd-dark-light-toggle` (no version number, no `-main`). That is how WordPress knows it is the *same* plugin. If the folder name changes, WordPress installs a second copy instead of updating.

## How to update

1. Go to **Plugins → Add New → Upload Plugin** and upload the new zip.
2. WordPress says the plugin is already installed. Click **Replace current with uploaded**.
3. Your settings stay as they are (they are saved in the database under one fixed name, `kd_settings`).

## How to set it up (2 minutes)

1. In your WordPress dashboard go to **Settings → KD Dark / Light**.
2. Choose **Light mode uses** (for example Palette 1).
3. Choose **Dark mode uses** (for example Palette 2). The two must be different.
4. Pick the switch size and where it should float (bottom right, top left, and so on).
5. Click **Save Changes**. Done!

Not sure what your palettes look like? The settings page shows the colours of Palette 1, 2 and 3 so you can see them.

If you want to change the colours themselves, go to **Appearance → Customize → Colors & Fonts → Colors → Global Palette** in Kadence.

## Ways to show the switch

| Way | How |
|---|---|
| Floating button | On by default. Choose the corner in settings. |
| Shortcode | Type `[kd_toggle]` in any page, post, HTML block or Shortcode block. |
| Widget | **Appearance → Widgets → KD Dark / Light Toggle**. |

You can also show the floating button on **every page**, **only some pages**, or **every page except some**, and hide it on any page, slug or URL path (for example `/checkout/`).

## Questions people ask

**Does it only work with Kadence?**
It is built for Kadence and reads Kadence's Global Palette. Other themes do not have that palette, so the switch will stay off.

**The switch does not show up. Why?**
Most common reasons: (1) both palettes are set to the same one, (2) you have never saved your Global Palette once in the Kadence Customizer, (3) the page is hidden in your visibility settings. The settings page tells you when the palettes are not found.

**Does it use cookies?**
No. The visitor's choice is kept in the browser's local storage only.

**Does it slow my site down?**
No. It adds a very small amount of CSS and JavaScript.

**Will it clash with another plugin?**
Everything in the code uses the unique prefix `kd_`, so it will not collide with other plugins.

**Can I use it for client websites?**
Yes. It is free for any use.

## Requirements

- WordPress 5.7 or newer
- PHP 7.4 or newer
- The Kadence theme with Global Palettes

## Changelog

**2.2.0**
- Renamed to KD Dark Light Toggle. Author: ASJ. New plugin folder `kd-dark-light-toggle`.

**2.1.2**
- Updating now replaces the existing plugin instead of adding a second copy (fixed folder name, fixed settings name). Settings saved by older versions are carried over automatically.

**2.1.1**
- Fixed a fatal "Cannot redeclare function" error when used together with other plugins. All functions, constants and the widget class now use the unique `kd_` / `KD_` prefix.
- Added LICENSE and README.

**2.1.0**
- First public version of the Dark / Light switch with auto dark mode, floating button, shortcode, widget and page visibility rules.

## Contributing

Found a bug or have an idea? Please open an **Issue** or send a **Pull Request**. Short and simple reports are welcome.

## License

Free software under the **GNU General Public License v2 or later (GPL-2.0-or-later)**.
You may use, copy, change and share it. See the [LICENSE](LICENSE) file.

Copyright (c) ASJ.
