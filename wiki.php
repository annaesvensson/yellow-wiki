<?php
// Wiki extension, https://github.com/annaesvensson/yellow-wiki

class YellowWiki {
    const VERSION = "0.9.4";
    public $yellow;         // access to API
    
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("wikiStartLocation", "auto");
        $this->yellow->system->setDefault("wikiNewLocation", "@title");
        $this->yellow->system->setDefault("wikiShortcutEntries", "0");
        $this->yellow->system->setDefault("wikiPaginationLimit", "30");
    }

    // Handle page meta data
    public function onParseMetaData($page) {
        if ($page===$this->yellow->page) {
            if ($page->get("layout")=="wiki-start" && !$this->yellow->toolbox->isLocationArguments()) {
                $page->set("layout", "wiki");
                $page->set("wikiStart", "1");
            }
        }
        if ($page->get("layout")=="wiki") $page->set("editNewLocation", $this->yellow->system->get("wikiNewLocation"));
    }
    
    // Handle page content element
    public function onParseContentElement($page, $name, $text, $attributes, $type) {
        $output = null;
        if (substru($name, 0, 4)=="wiki" && ($type=="block" || $type=="inline")) {
            switch($name) {
                case "wikiauthors": $output = $this->getShortcutWikiauthors($page, $name, $text); break;
                case "wikitags":    $output = $this->getShortcutWikitags($page, $name, $text); break;
                case "wikipages":   $output = $this->getShortcutWikipages($page, $name, $text); break;
            }
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShortcutWikiauthors($page, $name, $text) {
        $output = null;
        list($startLocation, $shortcutEntries) = $this->yellow->toolbox->getTextArguments($text);
        if (is_string_empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (is_string_empty($shortcutEntries)) $shortcutEntries = $this->yellow->system->get("wikiShortcutEntries");
        $wikiStart = $this->getWikiStart($page, $startLocation);
        if (!is_null($wikiStart)) {
            $pages = $this->getWikiPages($wikiStart);
            $page->setLastModified($pages->getModified());
            $authors = $pages->group("author", false, "count");
            if ($shortcutEntries!=0) $authors = array_slice($authors, 0, $shortcutEntries, true);
            uksort($authors, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($authors as $author=>$collection) {
                $output .= "<li><a href=\"".$wikiStart->getLocation(true).$this->yellow->lookup->normaliseArguments("author:$author")."\">";
                $output .= htmlspecialchars($author)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikiauthors '$startLocation' does not exist!");
        }
        return $output;
    }

    // Return wikitags shortcut
    public function getShortcutWikitags($page, $name, $text) {
        $output = null;
        list($startLocation, $shortcutEntries) = $this->yellow->toolbox->getTextArguments($text);
        if (is_string_empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (is_string_empty($shortcutEntries)) $shortcutEntries = $this->yellow->system->get("wikiShortcutEntries");
        $wikiStart = $this->getWikiStart($page, $startLocation);
        if (!is_null($wikiStart)) {
            $pages = $this->getWikiPages($wikiStart);
            $page->setLastModified($pages->getModified());
            $tags = $pages->group("tag", false, "count");
            if ($shortcutEntries!=0) $tags = array_slice($tags, 0, $shortcutEntries, true);
            uksort($tags, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($tags as $tag=>$collection) {
                $output .= "<li><a href=\"".$wikiStart->getLocation(true).$this->yellow->lookup->normaliseArguments("tag:$tag")."\">";
                $output .= htmlspecialchars($tag)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikitags '$startLocation' does not exist!");
        }
        return $output;
    }
    
    // Return wikipages shortcut
    public function getShortcutWikipages($page, $name, $text) {
        $output = null;
        list($startLocation, $shortcutEntries, $filterTag) = $this->yellow->toolbox->getTextArguments($text);
        if (is_string_empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (is_string_empty($shortcutEntries)) $shortcutEntries = $this->yellow->system->get("wikiShortcutEntries");
        $wikiStart = $this->getWikiStart($page, $startLocation);
        if (!is_null($wikiStart)) {
            $pages = $this->getWikiPages($wikiStart, false)->remove($page);
            $page->setLastModified($pages->getModified());
            if (!is_string_empty($filterTag)) $pages->filter("tag", $filterTag);
            $pages->sort("title");
            if ($shortcutEntries!=0) $pages->limit($shortcutEntries);
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($pages as $pageWiki) {
                $output .= "<li><a".($pageWiki->isExisting("tag") ? " class=\"".$this->getClass($pageWiki)."\"" : "");
                $output .= " href=\"".$pageWiki->getLocation(true)."\">".$pageWiki->getHtml("title")."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikipages '$startLocation' does not exist!");
        }
        return $output;
    }
    
    // Handle page layout
    public function onParsePageLayout($page, $name) {
        if ($name=="wiki-start") {
            $chronologicalOrder = false;
            $pages = $this->getWikiPages($page);
            $pagesFilter = array();
            if ($page->getRequest("special")=="pages") {
                array_push($pagesFilter, $this->yellow->language->getText("wikiSpecialPages"));
            }
            if ($page->getRequest("special")=="changes") {
                $chronologicalOrder = true;
                array_push($pagesFilter, $this->yellow->language->getText("wikiSpecialChanges"));
            }
            if ($page->isRequest("tag")) {
                $pages->filter("tag", $page->getRequest("tag"));
                array_push($pagesFilter, $pages->getFilter());
            }
            if ($page->isRequest("author")) {
                $pages->filter("author", $page->getRequest("author"), false);
                array_push($pagesFilter, $pages->getFilter());
            }
            if ($page->isRequest("modified")) {
                $pages->filter("modified", $page->getRequest("modified"), false);
                array_push($pagesFilter, $this->yellow->language->getDateStandard($pages->getFilter()));
            }
            $pages->sort($chronologicalOrder ? "modified" : "title", !$chronologicalOrder);
            if (!is_array_empty($pagesFilter)) {
                $text = implode(" ", $pagesFilter);
                $this->yellow->page->set("titleHeader", $text." - ".$this->yellow->page->get("sitename"));
                $this->yellow->page->set("titleContent", $this->yellow->page->get("title").": ".$text);
                $this->yellow->page->set("title", $this->yellow->page->get("title").": ".$text);
                $this->yellow->page->set("wikiChronologicalOrder", $chronologicalOrder);
            }
            $this->yellow->page->setPages("wiki", $pages);
            $this->yellow->page->setLastModified($pages->getModified());
            $this->yellow->page->setHeader("Cache-Control", "max-age=60");
        }
        if ($name=="wiki") {
            $wikiStartLocation = $this->yellow->system->get("wikiStartLocation");
            if ($wikiStartLocation=="auto") {
                $wikiStart = $this->yellow->lookup->isFileLocation($page->location) ?  $page->getParent() : $page;
            } else {
                $wikiStart = $this->yellow->content->find($wikiStartLocation);
            }
            $this->yellow->page->setPage("wikiStart", $wikiStart);
        }
    }
    
    // Return wiki start page, null if not found
    public function getWikiStart($page, $wikiStartLocation) {
        if ($wikiStartLocation=="auto") {
            $wikiStart = null;
            foreach ($this->yellow->content->top(true, false) as $pageTop) {
                if ($pageTop->get("layout")=="wiki-start" || $pageTop->get("wikiStart")) {
                    $wikiStart = $pageTop;
                    break;
                }
            }
            if ($page->get("layout")=="wiki-start" || $page->get("wikiStart")) $wikiStart = $page;
        } else {
            $wikiStart = $this->yellow->content->find($wikiStartLocation);
        }
        return $wikiStart;
    }
    
    // Return wiki pages for page
    public function getWikiPages($page) {
        if ($this->yellow->system->get("wikiStartLocation")=="auto") {
            $pages = $page->getChildren();
        } else {
            $pages = $this->yellow->content->index();
        }
        $pages->filter("layout", "wiki");
        $pages->append($this->yellow->content->find($page->location));
        return $pages;
    }
    
    // Return class for page
    public function getClass($page) {
        $class = "";
        if ($page->isExisting("tag")) {
            foreach (preg_split("/\s*,\s*/", $page->get("tag")) as $tag) {
                $class .= " tag-".$this->yellow->lookup->normaliseClass($tag);
            }
        }
        return trim($class);
    }
}
