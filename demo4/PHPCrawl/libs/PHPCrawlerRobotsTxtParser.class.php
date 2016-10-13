<?php
/**
 * Class for parsing robots.txt-files.
 *
 * @package phpcrawl
 * @internal
 */  
class PHPCrawlerRobotsTxtParser
{ 
  public function __construct()
  {
    // Init PageRequest-class
    if (!class_exists("PHPCrawlerHTTPRequest")) include_once($classpath."/PHPCrawlerHTTPRequest.class.php");
    $this->PageRequest = new PHPCrawlerHTTPRequest();
  }
  
  /**
   * Parses a robots.txt-file and returns regular-expression-rules corresponding to the containing "disallow"-rules
   * that are adressed to the given user-agent.
   *
   * @param PHPCrawlerURLDescriptor $BaseUrl           The root-URL all rules from the robots-txt-file should relate to
   * @param string                  $user_agent_string The useragent all rules from the robots-txt-file should relate to
   * @param string                  $robots_txt_uri    Optional. The location of the robots.txt-file as URI.
   *                                                   If not set, the default robots.txt-file for the given BaseUrl gets parsed.
   *
   * @return array Numeric array containing regular-expressions for each "disallow"-rule defined in the robots.txt-file
   *               that's adressed to the given user-agent.
   */
  public function parseRobotsTxt(PHPCrawlerURLDescriptor $BaseUrl, $user_agent_string, $robots_txt_uri = null)
  {
    PHPCrawlerBenchmark::start("processing_robotstxt");
    
    // If robots_txt_uri not given, use the default one for the given BaseUrl
    if ($robots_txt_uri === null)
      $robots_txt_uri = self::getRobotsTxtURL($BaseUrl->url_rebuild);
    
    // Get robots.txt-content
    $robots_txt_content = PHPCrawlerUtils::getURIContent($robots_txt_uri, $user_agent_string);

    $non_follow_reg_exps = array();
    
    // If content was found
    if ($robots_txt_content != null)
    {
      // Get all lines in the robots.txt-content that are adressed to our user-agent.
      $applying_lines = $this->getUserAgentLines($robots_txt_content, $user_agent_string);
      
      // Get valid reg-expressions for the given disallow-pathes.
      $non_follow_reg_exps = $this->buildRegExpressions($applying_lines, PHPCrawlerUtils::getRootUrl($BaseUrl->url_rebuild));
    }
    
    PHPCrawlerBenchmark::stop("processing_robots.txt");
    
    return $non_follow_reg_exps;
  }
  
  /**
   * Gets all raw lines from the given robots.txt-content that apply to
   * the given useragent-string.
   *
   * @return array Numeric array containing the lines
   */
  protected function getUserAgentLines(&$robots_txt_content, $user_agent_string)
  {
    // Split the content into its lines
    $robotstxt_lines = explode("\n", $robots_txt_content);
    
    $user_agent_lines = array();
    $current_user_agent = null;
    
    // Loop over the lines and check if any user-agent-sections match with our agent
    $cnt = count($robotstxt_lines);
    for ($x=0; $x<$cnt; $x++)
    {
      $line = trim($robotstxt_lines[$x]);
      
      if ($line == "") continue;
      
      // Check if a line begins with "User-agent"
      if (preg_match("#^User-agent:\s*(.*)# i", $line, $match))
      {
        if (isset($match[1]))
          $current_user_agent = trim($match[1]);
        else
          $current_user_agent = "";
        
        continue;
      }
      
      // If User-Agent matches with our user-agent-string
      if ($current_user_agent == "*" || strtolower($current_user_agent) == strtolower($user_agent_string))
      {
        $user_agent_lines[] = trim($line);
      }
    }
    
    return $user_agent_lines;
  }
  
  /**
   * Returns an array containig regular-expressions corresponding
   * to the given robots.txt-style "Disallow"-lines
   *
   * @param array &$applying_lines Numeric array containing "disallow"-lines.
   * @param string $base_url       Base-URL the robots.txt-file was found in.
   *
   * @return array  Numeric array containing regular-expresseions created for each "disallow"-line.
   */
  protected function buildRegExpressions($applying_lines, $base_url)
  { 
    // First, get all "Disallow:"-pathes
    $disallow_pathes = array();
    
    $cnt = count($applying_lines);
    for ($x=0; $x<$cnt; $x++)
    {
      preg_match("#^Disallow:\s*(.*)# i", $applying_lines[$x], $match);
      
      if (!empty($match[1]))
      {
        $path = trim($match[1]);
        
        // Add leading slash
        if (substr($path, 0, 1) != "/")
          $path = "/".$path;
        
        $disallow_pathes[] = $path;
      }
    }
    
    // Works like this:
    // The base-url is http://www.foo.com.
    // The driective says: "Disallow: /bla/"
    // This means: The nonFollowMatch is "#^http://www\.foo\.com/bla/#"
    
    $normalized_base_url = PHPCrawlerUtils::normalizeURL($base_url);
    
    $non_follow_expressions = array();
    
    $cnt = count($disallow_pathes);
    for ($x=0; $x<$cnt; $x++)
    { 
      $non_follow_path_complpete = $normalized_base_url.$disallow_pathes[$x]; // "http://www.foo.com/bla/"
      $non_follow_exp = preg_quote($non_follow_path_complpete, "#"); // "http://www\.foo\.com/bla/"
      $non_follow_exp = "#^".$non_follow_exp."#"; // "#^http://www\.foo\.com/bla/#"
        
      $non_follow_expressions[] = $non_follow_exp;
    }
    
    return $non_follow_expressions;
  }
  
  /** 
   * Returns the default Robots.txt-URL related to the given URL
   *
   * @param string $url The URL
   * @return string Url of the related robots.txt file
   */
  public static function getRobotsTxtURL($url)
  {
    $url_parts = PHPCrawlerUtils::splitURL($url); 
    $robots_txt_url = $url_parts["protocol"].$url_parts["host"].":".$url_parts["port"] . "/robots.txt";
    
    return $robots_txt_url;
  }
}
  
?>