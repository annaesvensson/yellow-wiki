<p align="right"><a href="README-de.md">Deutsch</a> &nbsp; <a href="README.md">English</a> &nbsp; <a href="README-sv.md">Svenska</a></p>

# Wiki 0.8.24

Wiki för din webbplats.

<p align="center"><img src="wiki-screenshot.png?raw=true" alt="Skärmdump"></p>

## Hur man installerar ett tillägg

[Ladda ner ZIP-filen](https://github.com/annaesvensson/yellow-wiki/archive/main.zip) och kopiera den till din `system/extensions` mapp. [Läs mer om tillägg](https://github.com/annaesvensson/yellow-update/tree/main/README-sv.md).

## Hur man använder en wiki

Wikin finns på din webbplats som `http://website/wiki/`. För att skapa en ny wikisida, lägg till en ny fil i wiki-mappen. Ställ in `Title` och andra [sidinställningar](https://github.com/annaesvensson/yellow-core/tree/main/README-sv.md#inställningar-page) högst upp på en sida. Använd `Tag` för att gruppera liknande sidor.

## Hur man redigerar en wiki

Om du vill redigera wikisidor i en [webbläsare](https://github.com/annaesvensson/yellow-edit/tree/main/README-sv.md) kan du göra detta på din webbplats på `http://website/edit/wiki/`. Om du vill redigera wikisidor på din [dator](https://github.com/annaesvensson/yellow-core/tree/main/README-sv.md), ta en titt på `content/2-wiki` mappen. Här är några tips. Prefix och suffix tas bort från adressen, så att det ser bättre ut. Mappen `content/2-wiki` är tillgänglig på din webbplats som `http://website/wiki/`. Filen `content/2-wiki/wiki-example.md` är tillgänglig på din webbplats som `http://website/wiki/wiki-example`.

## Hur man visar wikiinformation

Du kan använda förkortningar för att visa information om wikin:

`[wikiauthors]` för en lista över författare  
`[wikitags]` för en lista med taggar  
`[wikipages]` för en lista med sidor, alfabetisk ordning  

Följande argument är tillgängliga, alla utom det första argumentet är valfria:

`StartLocation` = plats för wikistartsida  
`EntriesMax` = antal inlägg att visa per förkortning, 0 för obegränsad  
`FilterTag` = visa sidor med en specifik tagg, endast `[wikipages]`  

## Exempel

Innehållsfil för wikin:

    ---
    Title: Wikisida
    Layout: wiki
    Tag: Exempel
    ---
    Detta är ett exempel på en wikisida.

    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
    labore et dolore magna pizza. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
    nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
    esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt 
    in culpa qui officia deserunt mollit anim id est laborum.

Innehållsfil för wikin:

    ---
    Title: Kaffe är bra för dig
    Layout: wiki
    Tag: Exempel, Kaffe
    ---
    Kaffe är en dryck gjord av rostade bönor från kaffeplanten.
    
     1. Börja med färskt kaffe. Kaffebönor börjar tappa kvalitet direkt efter
        rostning och malning. Du får det bästa kaffe när de nymalda bönorna 
        bearbetas omedelbart.
     2. Brygg en kopp kaffe. Kaffe kan tillagas med olika metoder och 
        ytterligare smakämnen som mjölk och socker. Det finns espresso, 
        filterkaffe, fransk press, italiensk moka, turkiskt kaffe och många 
        fler. Hitta din favoritsmak,
     3. Njut. 

Innehållsfil med wikiinformation:

    ---
    Title: Översikt
    ---
    ## Sidor

    [wikipages]

    ## Taggar

    [wikitags]

Visa lista med sidor, olika antal inlägg:

    [wikipages /wiki/ 0]
    [wikipages /wiki/ 3]
    [wikipages /wiki/ 10]

Visa lista med sidor, med en specifik tagg:

    [wikipages /wiki/ 0 kaffe]
    [wikipages /wiki/ 0 mjölk]
    [wikipages /wiki/ 0 exempel]

Visa länkar till wikin:

    [Se alla sidor](/wiki/special:pages/)
    [Se senaste ändringarna](/wiki/special:changes/)
    [Se sidor av Datenstrom](/wiki/author:datenstrom/)
    [Se sidor om kaffe](/wiki/tag:kaffe/)
    [Se sidor med exempel](/wiki/tag:exempel/)

Konfigurera wikiadress i inställningar, URL identifieras automatiskt:

    WikiStartLocation: auto
    WikiNewLocation: @title

Konfigurera wikiadress i inställningar, URL med undermapp för författare:

    WikiStartLocation: /wiki/
    WikiNewLocation: /wiki/@author/@title

Konfigurera wikiadress i inställningar, URL med undermapp för kategorisering:

    WikiStartLocation: /wiki/
    WikiNewLocation: /wiki/@tag/@title

## Inställningar

Följande inställningar kan konfigureras i filen `system/extensions/yellow-system.ini`:

`WikiStartLocation` = plats för wikistartsida, `auto` för automatisk detektering  
`WikiNewLocation` = plats för nya wikisidor, [stödda platshållare](#inställningar-placeholders)  
`WikiEntriesMax` = antal inlägg att visa per förkortning, 0 för obegränsad  
`WikiPaginationLimit` = antal inlägg att visa per sida, 0 för obegränsad  

<a id="inställningar-placeholders"></a>Följande platshållare för nya wikisidor stöds:

`@title` = namn på sidan  
`@author` = sedans författare  
`@tag` = taggar för kategorisering av sidan  

<a id="inställningar-files"></a>Följande filer kan anpassas:

`content/shared/page-new-wiki.md` = innehållsfil för ny wikisida  
`system/layouts/wiki.html` = layoutfil för enskild wikisida  
`system/layouts/wiki-start.html` = layoutfil för wikistartsida  

## Utvecklare

Anna Svensson. [Få hjälp](https://datenstrom.se/sv/yellow/help/).
