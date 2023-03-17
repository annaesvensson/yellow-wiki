<?php
// Wiki extension, https://github.com/annaesvensson/yellow-wiki

class YellowWiki {
    const VERSION = "0.8.24";
    public $yellow;         // access to API
    
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("wikiStartLocation", "auto");
        $this->yellow->system->setDefault("wikiNewLocation", "@title");
        $this->yellow->system->setDefault("wikiEntriesMax", "5");
        $this->yellow->system->setDefault("wikiPaginationLimit", "30");
    }

    // Handle page meta data
    public function onParseMetaData($page) {
        if ($page===$this->yellow->page) {
            if ($page->get("layout")=="wiki-start" && !$this->yellow->toolbox->isLocationArguments()) {
                $page->set("layout", "wiki");
            }
        }
    }
    
    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if (substru($name, 0, 4)=="wiki" && ($type=="block" || $type=="inline")) {
            switch($name) {
                case "wikiauthors": $output = $this->getShorcutWikiauthors($page, $name, $text); break;
                case "wikitags":    $output = $this->getShorcutWikitags($page, $name, $text); break;
                case "wikipages":   $output = $this->getShorcutWikipages($page, $name, $text); break;
            }
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikiauthors($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (is_string_empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (is_string_empty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $wikiStart = $this->getWikiStart($page, $startLocation);
        if (!is_null($wikiStart)) {
            $pages = $this->getWikiPages($wikiStart);
            $page->setLastModified($pages->getModified());
            $authors = $this->getMeta($pages, "author");
            if ($entriesMax!=0 && count($authors)>$entriesMax) {
                uasort($authors, "strnatcasecmp");
                $authors = array_slice($authors, -$entriesMax, $entriesMax, true);
            }
            uksort($authors, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($authors as $key=>$value) {
                $output .= "<li><a href=\"".$wikiStart->getLocation(true).$this->yellow->lookup->normaliseArguments("author:$key")."\">";
                $output .= htmlspecialchars($key)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikiauthors '$startLocation' does not exist!");
        }
        return $output;
    }

    // Return wikitags shortcut
    public function getShorcutWikitags($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (is_string_empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (is_string_empty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $wikiStart = $this->getWikiStart($page, $startLocation);
        if (!is_null($wikiStart)) {
            $pages = $this->getWikiPages($wikiStart);
            $page->setLastModified($pages->getModified());
            $tags = $this->getMeta($pages, "tag");
            if ($entriesMax!=0 && count($tags)>$entriesMax) {
                uasort($tags, "strnatcasecmp");
                $tags = array_slice($tags, -$entriesMax, $entriesMax, true);
            }
            uksort($tags, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($tags as $key=>$value) {
                $output .= "<li><a href=\"".$wikiStart->getLocation(true).$this->yellow->lookup->normaliseArguments("tag:$key")."\">";
                $output .= htmlspecialchars($key)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikitags '$startLocation' does not exist!");
        }
        return $output;
    }
    
    // Return wikipages shortcut
    public function getShorcutWikipages($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax, $filterTag) = $this->yellow->toolbox->getTextArguments($text);
        if (is_string_empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (is_string_empty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $wikiStart = $this->getWikiStart($page, $startLocation);
        if (!is_null($wikiStart)) {
            $pages = $this->getWikiPages($wikiStart, false);
            $page->setLastModified($pages->getModified());
            if (!is_string_empty($filterTag)) $pages->filter("tag", $filterTag);
            $pages->sort("title");
            if ($entriesMax!=0) $pages->limit($entriesMax);
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
                array_push($pagesFilter, $this->yellow->language->normaliseDate($pages->getFilter()));
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
    
    // Handle content file editing
    public function onEditContentFile($page, $action, $email) {
        if ($page->get("layout")=="wiki") $page->set("editNewLocation", $this->yellow->system->get("wikiNewLocation"));
    }
    
    // Return wiki start page, null if not found
    public function getWikiStart($page, $wikiStartLocation) {
        if ($wikiStartLocation=="auto") {
            $wikiStart = null;
            foreach ($this->yellow->content->top(true, false) as $pageTop) {
                if ($pageTop->get("layout")=="wiki-start") {
                    $wikiStart = $pageTop;
                    break;
                }
            }
            if ($page->get("layout")=="wiki-start") $wikiStart = $page;
        } else {
            $wikiStart = $this->yellow->content->find($wikiStartLocation);
        }
        return $wikiStart;
    }
    
    // Return wiki pages for page
    public function getWikiPages($page, $includeWikiStart = true) {
        if ($this->yellow->system->get("wikiStartLocation")=="auto") {
            $pages = $page->getChildren();
        } else {
            $pages = $this->yellow->content->index();
        }
        $pages->filter("layout", "wiki");
        if ($includeWikiStart) {
            $wikiStart = $this->yellow->content->find($page->location);
            $pages->append($wikiStart);
        }
        return $pages;
    }
    
    // Return class for page
    public function getClass($page) {
        $class = "";
        if ($page->isExisting("tag")) {
            foreach (preg_split("/\s*,\s*/", $page->get("tag")) as $tag) {
                $class .= " tag-".$this->yellow->lookup->normaliseArguments($tag, false);
            }
        }
        return trim($class);
    }
    
    // Return meta data from page collection
    public function getMeta($pages, $key) {
        $data = array();
        foreach ($pages as $page) {
            if ($page->isExisting($key)) {
                foreach (preg_split("/\s*,\s*/", $page->get($key)) as $entry) {
                    if (!isset($data[$entry])) $data[$entry] = 0;
                    ++$data[$entry];
                }
            }
        }
        return $this->yellow->lookup->normaliseArray($data);
    }
}
