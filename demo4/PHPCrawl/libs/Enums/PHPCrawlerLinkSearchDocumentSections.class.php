<?php
/**
 * Possible values for defining the sections of HTML-documents that will get ignored by the internal link-finding algorythm.
 *
 * @package phpcrawl.enums
 */
class PHPCrawlerLinkSearchDocumentSections
{
  /**
   * Script-parts of html-documents (<script>...</script>)
   */
  const SCRIPT_SECTIONS = 1;
  
  /**
   * HTML-comments of html-documents (<!-->...<-->)
   */
  const HTML_COMMENT_SECTIONS = 2;
  
  /**
   * Javascript-triggering attributes like onClick, onMouseOver etc.
   */
  const JS_TRIGGERING_SECTIONS = 4;
  
  /**
   * All of the listed sections
   */
  const ALL_SPECIAL_SECTIONS = 7;
}
?>