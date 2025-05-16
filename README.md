# Chess_XH

Chess_XH facilitates to display simple chess game viewers on your website.
Contrary to many other viewers it doesn't require JavaScript or Flash.
The chess games can't be entered in CMSimple_XH's back-end; instead they have
to be imported from [PGN](https://en.wikipedia.org/wiki/Portable_Game_Notation) files.

## Table of Contents

  - [Requirements](#requirements)
  - [Download](#download)
  - [Installation](#installation)
  - [Settings](#settings)
  - [Usage](#usage)
  - [Limitations](#limitations)
  - [Troubleshooting](#troubleshooting)
  - [License](#license)
  - [Credits](#credits)

## Requirements

Chess_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.
Chess_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
if that is not already installed (see `Settings` → `Info`),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/chess_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.


1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole folder `chess/` to your server into the `plugins/`
   folder of  CMSimple_XH.
1. Set write permissions for the subfolders `css/` and `languages/`.
1. Check under `Plugins` → `Chess`, if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins in
the back-end of the website. Go to `Plugins` → `Chess`.

Localization is done under `Language`.  You can translate the character
strings to your own language if there is no appropriate language file
available, or customize them according to your needs.

The look of Chess_XH can be customized under `Stylesheet`.

## Usage

At first you have to import some PGN files into a proprietary format for
faster processing.  This can be done in the back-end of the website under
`Plugins` → `Chess` → `Import`. PGN files containing multiple games will result in
multiple `.dat` files with a single game each.

It is recommended to keep the PGN files after the import, as you will likely
need to import them again for future versions of the plugin.

To display a chess game on a page, insert

    {{{chess('italian')}}}

where `italian` is the name of a chess game in `.dat` format.

## Limitations

Currently only a small subset of the information available in PGN is used
by the plugin, namely the moves without comments or other anotations.

The import of a PGN file is quite time consuming, so very large PGN files
with many games might cause timeout issues (blank page). In this case you have
to split the PGN file manually before importing it.

The JavaScript support for improved viewing of the games requires a
contemporary browser. Old browsers such as IE 8 will fall back to plain HTML
forms.

## Troubleshooting

Report bugs and ask for support either on [Github](https://github.com/cmb69/chess_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Chess_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Chess_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Chess_XH.  If not, see <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

## Credits

This plugin has been inspired by [*Bulkington*](https://www.scroterturm.de/).

The plugin uses [chessParser](https://github.com/DHTMLGoodies/chessParser)
for the PGN import.
Many thanks to [Alf Magne Kalleland](http://dhtml-chess.com/)
for publishing this library under LGPL.

The plugin icon is designed by [Alessandro Rei](http://www.mentalrey.it/).
Many thanks for publishing this icon under GPL.

The images of the chess pieces are designed by [Colin M.L. Burnett](https://en.wikipedia.org/wiki/User:Cburnett).
Many thanks for publishing them on
[Wikimedia](https://commons.wikimedia.org/wiki/Category:SVG_chess_pieces/Standard_transparent)
under GPL.

The animated loading image is taken from [preloaders.net](https://preloaders.net/).
Many thanks for making this service available.


Many thanks to the community at the [CMSimple_XH Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.

<p>And last but not least many thanks to <a href="https://harteg.dk/">Peter
Harteg</a>, the "father" of CMSimple, and all developers of <a
href="https://www.cmsimple-xh.org/"> CMSimple_XH</a> without whom this amazing
CMS would not exist.</p>
