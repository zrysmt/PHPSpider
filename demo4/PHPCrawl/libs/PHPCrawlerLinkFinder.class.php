<?php
/**
 * Class for finding links in HTML-documents.
 *
 * @package phpcrawl
 * @internal
 */
class PHPCrawlerLinkFinder
{
  /**
   * Numeric array containing all tags to extract links from
   *
   * @var array
   */
  public $extract_tags = array("href", "src", "url", "location", "codebase", "background", "data", "profile", "action", "open");
  
  /**
   * Specifies whether links will also be searched outside of HTML-tags
   *
   * @var bool
   */
  public $aggressive_search = true;
  
  /**
   * Specifies whether redirect-links set in http-headers should get found.
   *
   * @var bool
   */
  public $find_redirect_urls = true;
  
  /**
   * The URL of the html-source to find links in
   *
   * @var PHPCrawlerURLDescriptor
   */
  protected $SourceUrl;
  
  /**
   * Cache for storing found links/urls
   *
   * @var PHPCrawlerURLCache
   */
  protected $LinkCache;
  
  /**
   * Flag indicating whether the top lines of the HTML-source were processed.
   */
  protected $top_lines_processed = false;
  
  /**
   * Parts of the base-url as PHPCrawlerUrlPartsDescriptor-object
   *
   * @var PHPCrawlerUrlPartsDescriptor
   */
  protected $baseUrlParts;
  
  protected $found_links_map = array();
  
  /**
   * Meta-attributes found in the html-source.
   *
   * @var array
   */
  protected $meta_attributes = array();
  
  /**
   * Sections of HTML-documents ignorded by the linkfinder.
   *
   * @var int Bitwise combination of PHPCrawlerLinkSearchDocumentSections-constants
   */
  protected $ignore_document_sections = null;
  
  public function __construct()
  {
    // Setup internal linkcache
    if (!class_exists("PHPCrawlerMemoryURLCache")) include_once(dirname(__FILE__)."/UrlCache/PHPCrawlerMemoryURLCache.class.php");
    $this->LinkCache = new PHPCrawlerMemoryURLCache();
    $this->LinkCache->url_distinct_property = PHPCrawlerURLCacheBase::URLHASH_URL;
    
    // PHPCrawlerLinkSearchDocumentSections-class
    if (!class_exists("PHPCrawlerLinkSearchDocumentSections")) include_once(dirname(__FILE__)."/Enums/PHPCrawlerLinkSearchDocumentSections.class.php");
  }
  
  /**
   * Sets the source-URL of the document to find links in
   *
   * @param PHPCrawlerURLDescriptor $SourceUrl
   */
  public function setSourceUrl(PHPCrawlerURLDescriptor $SourceUrl)
  {
    $this->SourceUrl = $SourceUrl;
    $this->baseUrlParts = PHPCrawlerUrlPartsDescriptor::fromURL($SourceUrl->url_rebuild);
  }
  
  /**
   * Processes the response-header of the document.
   *
   * @param &string $header The response-header of the document.
   */
  public function processHTTPHeader(&$header)
  {
    if ($this->find_redirect_urls == true)
    {
      $this->findRedirectLinkInHeader($header);
    }
  }
  
  /**
   * Resets/clears the internal link-cache.
   */
  public function resetLinkCache()
  {
    $this->LinkCache->clear();
    $this->top_lines_processed = false;
    $this->meta_attributes = array();
  }
  
  /**
   * Checks for a redirect-URL in the given http-header and adds it to the internal link-cache.
   */
  protected function findRedirectLinkInHeader(&$http_header)
  {
    PHPCrawlerBenchmark::start("checking_for_redirect_link");
    
    // Get redirect-URL or link from header
    $redirect_link = PHPCrawlerUtils::getRedirectURLFromHeader($http_header);
    
    // Add redirect-URL to linkcache
    if ($redirect_link != null)
    {
      $this->addLinkToCache($redirect_link, "", "", true);
    }
    
    PHPCrawlerBenchmark::stop("checking_for_redirect_link");
  }
  
  /**
   * Searches for links in the given HTML-chunk and adds found links the the internal link-cache.
   */
  public function findLinksInHTMLChunk(&$html_source)
  {
    PHPCrawlerBenchmark::start("searching_for_links_in_page");
    
    // Check for meta-base-URL and meta-tags in top of HTML-source
    if ($this->top_lines_processed == false)
    {
      $meta_base_url = PHPCrawlerUtils::getBaseUrlFromMetaTag($html_source);
      if ($meta_base_url != null)
      {
        $base_url = PHPCrawlerUtils::buildURLFromLink($meta_base_url, $this->baseUrlParts);
        $this->baseUrlParts = PHPCrawlerUrlPartsDescriptor::fromURL($base_url);
      }
      
      // Get all meta-tags
      $this->meta_attributes = PHPCrawlerUtils::getMetaTagAttributes($html_source);
      
      // Set flag that top-lines of source were processed
      $this->top_lines_processed = true;
    }
    
    // Prepare HTML-chunk
    $this->prepareHTMLChunk($html_source);
    
    // Build the RegEx-part for html-tags to search links in
    $tag_regex_part = "";
    $cnt = count($this->extract_tags);
    for ($x=0; $x<$cnt; $x++)
    {
      $tag_regex_part .= "|".$this->extract_tags[$x];
    }
    $tag_regex_part = substr($tag_regex_part, 1);
    
    // 1. <a href="...">LINKTEXT</a> (well formed link with </a> at the end and quotes around the link)
    // Get the link AND the linktext from these tags
    // This has to be done FIRST !!              
    preg_match_all("#<\s*a\s[^<>]*(?<=\s)(?:".$tag_regex_part.")\s*=\s*".
                   "(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))[^<>]*>".
                   "((?:(?!<\s*\/a\s*>).){0,500})".
                   "<\s*\/a\s*># is", $html_source, $matches);
                          
    $cnt = count($matches[0]);
    for ($x=0; $x<$cnt; $x++)
    {  
      $link_raw = trim($matches[1][$x]);
      $linktext = $matches[2][$x];
      $linkcode = trim($matches[0][$x]);

      if (!empty($link_raw)) $this->addLinkToCache($link_raw, $linkcode, $linktext);
    }
                   
    // Second regex (everything that could be a link inside of <>-tags)
    preg_match_all("#<[^<>]*\s(?:".$tag_regex_part.")\s*=\s*".
                   "(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))[^<>]*># is", $html_source, $matches);

    $cnt = count($matches[0]);
    for ($x=0; $x<$cnt; $x++)
    {
      $link_raw = trim($matches[1][$x]);
      $linktext = "";
      $linkcode = trim($matches[0][$x]);
      
      if (!empty($link_raw)) $this->addLinkToCache($link_raw, $linkcode, $linktext);
    }
    
    // Now, if agressive_mode is set to true, we look for some
    // other things
    $pregs = array();
    if ($this->aggressive_search == true)
    {
      // Links like "...:url("animage.gif")..."
      $pregs[]="/[\s\.:;](?:".$tag_regex_part.")\s*\(\s*([\"|']{0,1})([^\"'\) ]{1,500})['\"\)]/ is";
      
      // Everything like "...href="bla.html"..." with qoutes
      $pregs[]="/[\s\.:;\"'](?:".$tag_regex_part.")\s*=\s*([\"|'])(.{0,500}?)\\1/ is";
      
      // Everything like "...href=bla.html..." without qoutes
      $pregs[]="/[\s\.:;](?:".$tag_regex_part.")\s*(=)\s*([^\s\">']{1,500})/ is";
      
      for ($x=0; $x<count($pregs); $x++)
      {
        unset($matches);
        preg_match_all($pregs[$x], $html_source, $matches);
        
        $cnt = count($matches[0]);
        for ($y=0; $y<$cnt; $y++)
        {
          $link_raw = trim($matches[2][$y]);
          $linkcode = trim($matches[0][$y]);
          $linktext = "";
          
          $this->addLinkToCache($link_raw, $linkcode, $linktext);
        }
      }
    }
    
    $this->found_links_map = array();
    
    PHPCrawlerBenchmark::stop("searching_for_links_in_page");
  }
  
  /**
   * Prepares a chunk of HTML before links get searched in it
   */
  protected function prepareHTMLChunk(&$html_source)
  { 
    // WARNING:
    // When modifying, test thhe following regexes on a huge page for preg_replace segfaults.
    // Be sure to set the preg-groups to "non-capture" (?:...)!
    
    // Replace <script>-sections from source, but only those without src in it.
    if ($this->ignore_document_sections & PHPCrawlerLinkSearchDocumentSections::SCRIPT_SECTIONS)
    {
      $html_source = preg_replace("#<script(?:(?!src).)*>.*(?:<\/script>|$)# Uis", "", $html_source);
      $html_source = preg_replace("#^(?:(?!<script).)*<\/script># Uis", "", $html_source);
    }
    
    // Replace HTML-comments from source
    if ($this->ignore_document_sections & PHPCrawlerLinkSearchDocumentSections::HTML_COMMENT_SECTIONS)
    {
      $html_source = preg_replace("#<\!--.*(?:-->|$)# Uis", "", $html_source);
      $html_source = preg_replace("#^(?:(?!<\!--).)*--># Uis", "", $html_source);
    }
    
    // Replace javascript-triggering attributes
    if ($this->ignore_document_sections & PHPCrawlerLinkSearchDocumentSections::JS_TRIGGERING_SECTIONS)
    {
      $html_source = preg_replace("#on[a-z]+\s*=\s*(?|\"([^\"]+)\"|'([^']+)'|([^\s><'\"]+))# Uis", "", $html_source);
    }
  }
  
  /**
   * Adds a link to the LinkFinder-internal link-cache
   *
   * @param string $link_raw        The link like it was found
   * @param string $link_code       The html-code of the link like it was found (i.e. <a href="the_link.html">Link</a>)
   * @param string $link_text       The linktext like it was found
   * @param bool   $is_redirect_url Flag indicatin whether the found URL is target of an HTTP-redirect 
   */
  protected function addLinkToCache($link_raw, $link_code, $link_text = "", $is_redirect_url = false)
  {
    //PHPCrawlerBenchmark::start("preparing_link_for_cache");
    
    // If liks already was found and processed -> skip this link
    if (isset($this->found_links_map[$link_raw])) return;
    
    // Rebuild URL from link
    $url_rebuild = PHPCrawlerUtils::buildURLFromLink($link_raw, $this->baseUrlParts);

    // If link coulnd't be rebuild
    if ($url_rebuild == null) return;
    
    // Create an PHPCrawlerURLDescriptor-object with URL-data
    $url_link_depth = $this->SourceUrl->url_link_depth + 1;
    $UrlDescriptor = new PHPCrawlerURLDescriptor($url_rebuild, $link_raw, $link_code, $link_text, $this->SourceUrl->url_rebuild, $url_link_depth);
    
    // Is redirect-URL?
    if ($is_redirect_url == true)
      $UrlDescriptor->is_redirect_url = true;
    
    // Add the PHPCrawlerURLDescriptor-object to LinkCache
    $this->LinkCache->addURL($UrlDescriptor);
        
    // Add the PHPCrawlerURLDescriptor-object to found-links-array
    $map_key = $link_raw;
    $this->found_links_map[$map_key] = true;
    
    //PHPCrawlerBenchmark::stop("preparing_link_for_cache");
  }
  
  /**
   * Returns all URLs/links found so far in the document.
   *
   * @return array Numeric array containing all URLs as PHPCrawlerURLDescriptor-objects
   */
  public function getAllURLs()
  {
    return $this->LinkCache->getAllURLs();
  }
  
  /**
   * Returns all meta-tag attributes found so far in the document.
   *
   * @return array Assoziative array conatining all found meta-attributes.
   *               The keys are the meta-names, the values the content of the attributes.
   *               (like $tags["robots"] = "nofollow")
   *
   */
  public function getAllMetaAttributes()
  {
    return $this->meta_attributes;
  }
  
  /**
   * Defines the sections of a document that will get ignroed by the link-finder.
   *
   * @param int $document_sections Bitwise combination of the {@link PHPCrawlerLinkSearchDocumentSections}-constants.
   */
  public function excludeLinkSearchDocumentSections($document_sections)
  {
    $this->ignore_document_sections = $document_sections;
  }
}
?>