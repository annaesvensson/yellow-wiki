<p align="right"><a href="README-de.md">Deutsch</a> &nbsp; <a href="README.md">English</a> &nbsp; <a href="README-sv.md">Svenska</a></p>

# Wiki 0.8.30

Wiki for your website.

<p align="center"><img src="wiki-screenshot.png?raw=true" alt="Screenshot"></p>

## How to install an extension

[Download ZIP file](https://github.com/annaesvensson/yellow-wiki/archive/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## How to use a wiki

The wiki is available on your website as `http://website/wiki/`. To create a new wiki page, add a new file to the wiki folder. Set `Title` and other [page settings](https://github.com/annaesvensson/yellow-core#settings-page) at the top of a page. Use `Tag` to group similar pages together.

## How to edit a wiki

If you want to edit wiki pages in a [web browser](https://github.com/annaesvensson/yellow-edit), you can do this on your website at `http://website/edit/wiki/`. If you want to edit wiki pages on your [computer](https://github.com/annaesvensson/yellow-core), have a look inside your `content/2-wiki` folder. Here are some tips. At the top of a page you can change `Title` and other [page settings](https://github.com/annaesvensson/yellow-core#settings-page). Below you can change text and images. [Learn more about text formatting](https://datenstrom.se/yellow/help/how-to-change-the-content).

## How to show wiki information

You can use shortcuts to show information about the wiki:

`[wikiauthors]` for a list of authors  
`[wikitags]` for a list of tags  
`[wikipages]` for a list of pages, alphabetic order  

The following arguments are available:

`StartLocation` = location of wiki start page, `auto` for automatic detection  
`ShortcutEntries` = number of entries to show per shortcut, 0 for unlimited  
`FilterTag` = show pages with a specific tag, `[wikipages]` only  

## Examples

Content file for wiki:

    ---
    Title: Wiki example page
    Layout: wiki
    Tag: Example
    ---
    This is an example page.

    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
    labore et dolore magna pizza. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
    nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
    esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt 
    in culpa qui officia deserunt mollit anim id est laborum.

Content file for wiki:

    ---
    Title: Coffee is good for you
    Layout: wiki
    Tag: Example, Coffee
    ---
    Coffee is a beverage made from the roasted beans of the coffee plant.
    
    1. Start with fresh coffee. Coffee beans start losing quality immediately 
       after roasting and grinding. The best coffee is made from beans ground 
       right after roasting. 
    2. Brew a cup of coffee. Coffee is prepared with different methods and 
       additional flavorings such as milk and sugar. There are Espresso, Filter 
       coffee, French press, Italian Moka, Turkish coffee and many more. Find 
       out what's your favorite.
    3. Enjoy.

Content file with wiki information:

    ---
    Title: Overview
    ---
    ## Pages

    [wikipages]

    ## Tags

    [wikitags]

Showing list of pages, different number of entries:

    [wikipages /wiki/ 0]
    [wikipages /wiki/ 3]
    [wikipages /wiki/ 10]

Showing list of pages, with a specific tag:

    [wikipages /wiki/ 0 coffee]
    [wikipages /wiki/ 0 milk]
    [wikipages /wiki/ 0 example]

Showing links to wiki:

    [See all pages](/wiki/special:pages/)
    [See recent changes](/wiki/special:changes/)
    [See pages by Datenstrom](/wiki/author:datenstrom/)
    [See pages about coffee](/wiki/tag:coffee/)
    [See pages with examples](/wiki/tag:example/)

Configuring wiki address in the settings, URL is automatically detected:

    WikiStartLocation: auto
    WikiNewLocation: @title

Configuring wiki address in the settings, URL with subfolder for author:

    WikiStartLocation: /wiki/
    WikiNewLocation: /wiki/@author/@title

Configuring wiki address in the settings, URL with subfolder for categorisation:

    WikiStartLocation: /wiki/
    WikiNewLocation: /wiki/@tag/@title

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`WikiStartLocation` = location of wiki start page, `auto` for automatic detection  
`WikiNewLocation` = location for new wiki pages, [supported placeholders](#settings-placeholders)  
`WikiShortcutEntries` = number of entries to show per shortcut, 0 for unlimited  
`WikiPaginationLimit` = number of entries to show per page, 0 for unlimited  

<a id="settings-placeholders"></a>The following placeholders for new wiki pages are supported:

`@title` = page title  
`@author` = page author  
`@tag` = page tag for categorisation  

<a id="settings-files"></a>The following files can be customised:

`content/shared/page-new-wiki.md` = content file for new wiki page  
`system/layouts/wiki.html` = layout file for individual wiki page  
`system/layouts/wiki-start.html` = layout file for wiki start page  

## Developer

Anna Svensson. [Get help](https://datenstrom.se/yellow/help/).
