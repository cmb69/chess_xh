# Chess_XH

Chess_XH ermöglicht die Anzeige von einfachen Schachspiel-Viewern auf Ihrer
Website. Im Gegensatz zu vielen anderen Viewern wird weder JavaScript noch Flash
benötigt.
Die Schachspiele können nicht in CMSimple_XHs Back-End eingegeben,
sondern müssen aus [PGN](https://de.wikipedia.org/wiki/Portable_Game_Notation)
Dateien importiert werden.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Chess_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.
Chess_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/chess_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Computer.
1. Laden Sie das gesamte Verzeichnis `chess/` auf Ihren Server in den
   `plugins/` Ordner von CMSimple_XH hoch.
1. Vergeben Sie Schreibrechte für die Unterorder `css/` und `languages/`.
1. Prüfen Sie unter `Plugins` → `Chess`, ob alle
   Voraussetzungen für den Betrieb erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Website.
Gehen Sie zu `Plugins` → `Chess`.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder sie entsprechend Ihren Anforderungen
anpassen.

Das Aussehen von Chess_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Zunächst müssen Sie PGN Dateien in ein proprietäres Format zwecks schnellerer
Verarbeitung konvertieren. Dies erfolgt im Back-End der Website unter 
`Plugins` → `Chess` → `Import`. PGN Dateien mit mehreren Spielen ergeben mehrere
`.dat` Dateien mit jeweils einem Spiel.

Es wird empfohlen die PGN Dateien nach dem Import zu erhalten, da Sie diese
vermutlich für zukünftige Versionen des Plugins erneut importieren müssen.

Um ein Schachspiel auf einer Seite anzuzeigen, notieren Sie

    {{{chess('italian')}}}

wobei `italian` der Name eines Schachspiels im `.dat` Format ist.

## Einschränkungen

Derzeit wird nur eine kleine Menge der Informationen, die in PGN verfügbar
sind, vom Plugin genutzt, und zwar lediglich die Züge ohne Kommentare oder
andere Anmerkungen.

Der Import einer PGN Datei ist recht zeitaufwendig, so dass sehr große PGN
Dateien mit vielen Spielen einen Timeout (leere Seite) verursachen könnten. In
diesem Fall müssen Sie die PGN Datei vor dem Import manuell aufteilen.

Die JavaScript Unterstützung zum verbesserten Betrachten der Spiele erfordert
einen zeitgemäßen Browser. Alte Browser wie der IE 8 fallen auf einfache HTML
Formulare zurück.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/chess_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Chess_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Chess_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Chess_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

## Danksagung

Dieses Plugin wurde von [*Bulkington*](https://www.scroterturm.de/) angeregt.

Das Plugin verwendet [chessParser](https://github.com/DHTMLGoodies/chessParser)
für den PGN Import. Vielen Dank an [Alf Magne Kalleland](http://dhtml-chess.com/)
für die Veröffentlichung dieser Bibliothek unter LGPL.

Das Plugin-Icon wurde von [Alessandro Rei](http://www.mentalrey.it/) gestaltet.
Vielen Dank für die Veröffentlichung unter GPL.

Die Bilder der Schachsteine wurden von [Colin M.L. Burnett](https://en.wikipedia.org/wiki/User:Cburnett)
gestaltet. Vielen Dank für die Veröffentlichung auf
[Wikimedia](http://commons.wikimedia.org/wiki/Category:SVG_chess_pieces/Standard_transparent)
unter GPL.

Das animierte Lade-Bild stammt von [preloaders.net](https://preloaders.net/).
Vielen Dank für das zur Verfügung stellen dieses Dienstes.

Vielen Dank an die Gemeinschaft im [CMSimple_XH Forum](https://www.cmsimpleforum.com/)
für Tipps, Anregungen und das Testen.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
