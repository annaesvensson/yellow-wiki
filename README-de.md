<p align="right"><a href="README-de.md">Deutsch</a> &nbsp; <a href="README.md">English</a> &nbsp; <a href="README-sv.md">Svenska</a></p>

# Wiki 0.8.23

Wiki für deine Webseite.

<p align="center"><img src="wiki-screenshot.png?raw=true" alt="Bildschirmfoto"></p>

## Wie man ein Wiki benutzt

Das Wiki ist auf deiner Webseite vorhanden als `http://website/wiki/`. Um das Wiki auf der Startseite anzuzeigen, gehe in dein `content`-Verzeichnis und lösche das `1-home`-Verzeichnis. Um eine neue Wikiseite hinzuzufügen, erstelle eine neue Datei im Wikiverzeichnis. Ganz oben auf einer Seite kannst du `Title` und andere [Seiteneinstellungen](https://github.com/annaesvensson/yellow-core/tree/main/README-de.md#einstellungen-seite) festlegen. Mit `Tag` kann man ähnliche Seiten gruppieren. 

## Wie man Wikiinformationen anzeigt

Du kannst Abkürzungen verwenden, um Informationen über das Wiki anzuzeigen:

`[wikiauthors]` für eine Liste der Autoren  
`[wikitags]` für eine Liste der Tags  
`[wikirelated]` für eine Liste von Seiten, ähnlich zur aktuellen Seite    
`[wikipages]` für eine Liste von Seiten, alphabetische Reihenfolge  
`[wikichanges]` für eine Liste von Seiten, zuletzt veränderte Reihenfolge  

Die folgenden Argumente sind verfügbar, alle bis auf das erste Argument sind optional:

`StartLocation` = Ort der Wikistartseite  
`EntriesMax` = Anzahl der Einträge pro Abkürzung, 0 für unbegrenzt  
`FilterTag` = Seiten mit bestimmten Tag anzeigen, nur bei `[wikipages]` oder `[wikichanges]`  

## Beispiele

Inhaltsdatei fürs Wiki:

    ---
    Title: Wikiseite
    Layout: wiki
    Tag: Beispiel
    ---
    Das ist eine Beispielseite fürs Wiki.

    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
    labore et dolore magna pizza. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
    nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
    esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt 
    in culpa qui officia deserunt mollit anim id est laborum.

Inhaltsdatei fürs Wiki:

    ---
    Title: Kaffee ist gut für dich
    Layout: wiki
    Tag: Beispiel, Kaffee
    ---
    Kaffee ist ein Getränk aus gerösteten Bohnen der Kaffeepflanze.
    
    1. Verwende frischen Kaffee. Kaffeebohnen fangen nach dem Rösten und Mahlen 
       sofort an, an Qualität zu verlieren. Den besten Kaffee erhält man, wenn 
       man die frisch gemahlenen Bohnen sofort weiterverarbeitet.
    2. Eine Tasse Kaffee zubereiten. Kaffee kann durch vielerlei Methoden und mit 
       verschiedenen Zusätzen wie Milch und Zucker zubereitet werden. Es gibt 
       Espresso, Filterkaffee, Kaffee aus der französischen Presse, Italienischer 
       Mokka, Türkischen Kaffee und vieles mehr. Finde deinen Lieblingsgeschmack.
    3. Genieße.

Inhaltsdatei mit Wikiinformationen:

    ---
    Title: Übersicht
    ---
    ## Seiten

    [wikipages /wiki/ 0]

    ## Tags

    [wikitags /wiki/ 0]

Liste mit Seiten anzeigen, alphabetische Reihenfolge:

    [wikipages /wiki/ 0]
    [wikipages /wiki/ 3]
    [wikipages /wiki/ 10]

Liste mit Seiten anzeigen, alphabetische Reihenfolge mit einem bestimmten Tag:

    [wikipages /wiki/ 0 Kaffee]
    [wikipages /wiki/ 0 Milch]
    [wikipages /wiki/ 0 Beispiel]

Liste mit Seiten anzeigen, zuletzt veränderte Reihenfolge:

    [wikichanges /wiki/ 0]
    [wikichanges /wiki/ 3]
    [wikichanges /wiki/ 10]

Liste mit Seiten anzeigen, zuletzt veränderte Reihenfolge mit einem bestimmten Tag:

    [wikichanges /wiki/ 0 Kaffee]
    [wikichanges /wiki/ 0 Milch]
    [wikichanges /wiki/ 0 Beispiel]

Links zum Wiki anzeigen:

    [Siehe alle Seiten](/wiki/special:pages/)
    [Siehe letzte Änderungen](/wiki/special:changes/)
    [Siehe Seiten von Datenstrom](/wiki/author:datenstrom/)
    [Siehe Seiten über Kaffee](/wiki/tag:kaffee/)
    [Siehe Seiten mit Beispielen](/wiki/tag:beispiel/)

Wikistartseite in den Einstellungen festlegen, URL mit Unterverzeichnis zur Kategorisierung:

    WikiStartLocation: /wiki/
    WikiNewLocation: /wiki/@tag/@title

## Einstellungen

Die folgenden Einstellungen können in der Datei `system/extensions/yellow-system.ini` vorgenommen werden:

`WikiStartLocation` = Ort der Wikistartseite, `auto` für automatische Erkennung  
`WikiNewLocation` = Ort für neue Wikiseiten, [unterstützte Platzhalter](#einstellungen-placeholders)  
`WikiEntriesMax` = Anzahl der Einträge pro Abkürzung, 0 für unbegrenzt  
`WikiPaginationLimit` = Anzahl der Einträge pro Seite, 0 für unbegrenzt  

<a id="einstellungen-placeholders"></a>Die folgenden Platzhalter für neue Wikiseiten werden unterstützt:

`@title` = Seitentitel  
`@author` = Autor der Seite  
`@tag` = Tag zur Kategorisierung der Seite  

<a id="einstellungen-files"></a>Die folgenden Dateien können angepasst werden:

`content/shared/page-new-wiki.md` = Inhaltsdatei für neue Wikiseite  
`system/layouts/wiki.html` = Layoutdatei für individuelle Wikiseite  
`system/layouts/wiki-start.html` = Layoutdatei für die Wikistartseite  

## Installation

[Erweiterung herunterladen](https://github.com/annaesvensson/yellow-wiki/archive/main.zip) und die ZIP-Datei in dein `system/extensions`-Verzeichnis kopieren. [Weitere Informationen zu Erweiterungen](https://github.com/annaesvensson/yellow-update/tree/main/README-de.md).

## Entwickler

Anna Svensson. [Hilfe finden](https://datenstrom.se/de/yellow/help/).
