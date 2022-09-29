<?php
// Wiki extension, https://github.com/annaesvensson/yellow-wiki

class YellowWiki {
    const VERSION = "0.8.20";
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
                case "wikipages":   $output = $this->getShorcutWikipages($page, $name, $text); break;
                case "wikichanges": $output = $this->getShorcutWikichanges($page, $name, $text); break;
                case "wikirelated": $output = $this->getShorcutWikirelated($page, $name, $text); break;
                case "wikitags":    $output = $this->getShorcutWikitags($page, $name, $text); break;
            }
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikiauthors($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (strempty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $wikiStart = $this->yellow->content->find($startLocation);
        $pages = $this->getWikiPages($startLocation);
        $page->setLastModified($pages->getModified());
        $authors = $this->getMeta($pages, "author");
        if (count($authors)) {
            $authors = $this->yellow->lookup->normaliseUpperLower($authors);
            if ($entriesMax!=0 && count($authors)>$entriesMax) {
                uasort($authors, "strnatcasecmp");
                $authors = array_slice($authors, -$entriesMax, $entriesMax, true);
            }
            uksort($authors, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($authors as $key=>$value) {
                $output .= "<li><a href=\"".$wikiStart->getLocation(true).$this->yellow->toolbox->normaliseArguments("author:$key")."\">";
                $output .= htmlspecialchars($key)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikiauthors '$startLocation' does not exist!");
        }
        return $output;
    }

    // Return wikiauthors shortcut
    public function getShorcutWikipages($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax, $filterTag) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (strempty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $pages = $this->getWikiPages($startLocation, false);
        if (!empty($filterTag)) $pages->filter("tag", $filterTag);
        $pages->sort("title");
        $page->setLastModified($pages->getModified());
        if (count($pages)) {
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
        
    // Return wikiauthors shortcut
    public function getShorcutWikichanges($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax, $filterTag) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (strempty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $pages = $this->getWikiPages($startLocation);
        if (!empty($filterTag)) $pages->filter("tag", $filterTag);
        $pages->sort("modified", false);
        $page->setLastModified($pages->getModified());
        if (count($pages)) {
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
            $page->error(500, "Wikichanges '$startLocation' does not exist!");
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikirelated($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (strempty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $pages = $this->getWikiPages($startLocation);
        $pages->similar($page->getPage("main"));
        $page->setLastModified($pages->getModified());
        if (count($pages)) {
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
            $page->error(500, "Wikirelated '$startLocation' does not exist!");
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikitags($page, $name, $text) {
        $output = null;
        list($startLocation, $entriesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($startLocation)) $startLocation = $this->yellow->system->get("wikiStartLocation");
        if (strempty($entriesMax)) $entriesMax = $this->yellow->system->get("wikiEntriesMax");
        $wikiStart = $this->yellow->content->find($startLocation);
        $pages = $this->getWikiPages($startLocation);
        $page->setLastModified($pages->getModified());
        $tags = $this->getMeta($pages, "tag");
        if (count($tags)) {
            $tags = $this->yellow->lookup->normaliseUpperLower($tags);
            if ($entriesMax!=0 && count($tags)>$entriesMax) {
                uasort($tags, "strnatcasecmp");
                $tags = array_slice($tags, -$entriesMax, $entriesMax, true);
            }
            uksort($tags, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($tags as $key=>$value) {
                $output .= "<li><a href=\"".$wikiStart->getLocation(true).$this->yellow->toolbox->normaliseArguments("tag:$key")."\">";
                $output .= htmlspecialchars($key)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikitags '$startLocation' does not exist!");
        }
        return $output;
    }
    
    // Handle page layout
    public function onParsePageLayout($page, $name) {
        if ($name=="wiki-start") {
            $chronologicalOrder = false;
            $pages = $this->getWikiPages($this->yellow->page->location);
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
            if (!empty($pagesFilter)) {
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
            if ($wikiStartLocation!="auto") {
                $wikiStart = $this->yellow->content->find($wikiStartLocation);
            } else {
                $wikiStart = $this->yellow->lookup->isFileLocation($page->location) ?  $page->getParent() : $page;                
            }
            $this->yellow->page->setPage("wikiStart", $wikiStart);
        }
    }
    
    // Handle content file editing
    public function onEditContentFile($page, $action, $email) {
        if ($page->get("layout")=="wiki") $page->set("pageNewLocation", $this->yellow->system->get("wikiNewLocation"));
    }
    
    // Return wiki pages
    public function getWikiPages($location, $includeWikiStart = true) {
        $pages = $this->yellow->content->clean();
        $wikiStart = $this->yellow->content->find($location);
        if ($wikiStart && $wikiStart->get("layout")=="wiki-start") {
            if ($this->yellow->system->get("wikiStartLocation")!="auto") {
                $pages = $this->yellow->content->index();
            } else {
                $pages = $wikiStart->getChildren();
            }
            $pages->filter("layout", "wiki");
            if ($includeWikiStart) $pages->append($wikiStart);
        }
        return $pages;
    }
    
    // Return class for page
    public function getClass($page) {
        $class = "";
        if ($page->isExisting("tag")) {
            foreach (preg_split("/\s*,\s*/", $page->get("tag")) as $tag) {
                $class .= " tag-".$this->yellow->toolbox->normaliseArguments($tag, false);
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
        return $data;
    }
}
