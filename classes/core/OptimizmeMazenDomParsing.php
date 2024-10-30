<?php

namespace Optimizme\Mazen;

use \Sunra\PhpSimple\HtmlDomParser;

/**
 * Class OptimizmeMazenDomParsing
 * @package Optimizme\Mazen
 */
class OptimizmeMazenDomParsing
{
    public $isDOMDocument;
    public $doc;
    public $dom;
    public $tag;
    public $attributes;
    public $nodes;
    public $nbNodes;
    public $boolSave;
    public $tabSuccess;
    public $tabError;

    /**
     * OptimizmeMazenDomParsing constructor.
     */
    public function __construct()
    {
        $this->boolSave = 0;
        $this->tabSuccess = [];
        $this->tabError = [];

        if (class_exists("DOMDocument")) {
            $this->isDOMDocument = 1;
            $this->doc = new \DOMDocument;
        } else {
            $this->isDOMDocument = 0;
        }
    }

    /**
     * @param $content
     * @param $tag
     */
    public function getNodesInContent($content, $tag)
    {
        if ($this->isDOMDocument == 1) {
            // load post content in DOM
            libxml_use_internal_errors(true);
            $this->doc->loadHTML('<span>' . $content . '</span>');
            libxml_clear_errors();

            // get all tags in content
            $xp = new \DOMXPath($this->doc);
            $this->nodes = $xp->query('//' . $tag);
        } else {
            // without DOMDocument
            if (trim($content) != '') {
                $this->dom = HtmlDomParser::str_get_html($content);
                $this->nodes = $this->dom->find($tag);
            }
        }
        $this->getNodesCount();
    }

    /**
     * Return all nodes value
     */
    public function getNodesValues()
    {
        $res = [];
        if ($this->nbNodes > 0) {
            foreach ($this->nodes as $node) {
                $res[] = $this->getNodeValue($node);
            }
        }
        return $res;
    }

    /**
     * @return int
     */
    public function getNodesCount()
    {
        if ($this->isDOMDocument == 1) {
            $this->nbNodes = $this->nodes->length;
        } else {
            $this->nbNodes = count($this->nodes);
        }
    }

    /**
     * @param array $values
     * @param string $attr
     * @param int $returnTab : to return remaining values
     * @return array|int
     */
    public function changeNodesValues($values, $attr = '', $returnTab = 0)
    {
        // other strings to array (for bulk mode)
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($this->nodes as $cpt => $node) {
            if (is_array($values) && isset($values[$cpt])) {
                $this->changeNodeValue($node, $values[$cpt], $attr);
                $this->boolSave = 1;
                array_push($this->tabSuccess, $values[$cpt]);

                if ($returnTab == 1) {
                    unset($values[$cpt]);
                }
            } else {
                array_push($this->tabError, __('DOM Parsing node element number ' . ($cpt + 1) . ': no data sent by Mazen, so it was not modified in CMS', 'mazen-seo-connector'));
            }
        }

        if ($returnTab == 1) {
            $value = array_values($values);
            return $value;
        }
    }

    /**
     * Change node content
     * @param $node
     * @param string $value : new value to set
     * @param string $attr : attribute to edit (optionnal)
     */
    public function changeNodeValue($node, $value, $attr = '')
    {
        if ($this->isDOMDocument == 1) {

            // With DOMDocument
            if ($attr != '') {
                $newVal = utf8_encode($value);
                if (trim($newVal) == '') {
                    $node->removeAttribute($attr);
                } else {
                    $node->setAttribute($attr, $newVal);
                }
            } else {
                $node->nodeValue = $value;
            }
        } else {
            // with Simple HTML Dom Parser
            if ($attr != '') {
                $node->{$attr} = $value;
            } else {
                // change value
                $node->setAttribute('innertext', $value);
            }
        }
    }

    /**
     * @param $post
     * @param $tag
     * @return array|mixed|void
     */
    public function extractValuesFromNodes($post, $tag)
    {
        $content = $post->post_content;

        $this->getNodesInContent($content, $tag);
        $tabTags = [];

        if ($this->nbNodes > 0) {
            foreach ($this->nodes as $node) {
                if ($tag == 'a') {
                    $tabLoop = [
                        'anchor' => $this->getNodeValue($node),
                        'href' => $this->getNodeValue($node, 'href'),
                        'target' => $this->getNodeValue($node, 'target'),
                        'rel' => $this->getNodeValue($node, 'rel'),
                    ];
                    array_push($tabTags, $tabLoop);
                } elseif ($tag == 'img') {
                    $tabLoop = [
                        'src' => $this->getNodeValue($node, 'src'),
                        'alt' => $this->getNodeValue($node, 'alt'),
                        'title' => $this->getNodeValue($node, 'title'),
                    ];
                    array_push($tabTags, $tabLoop);
                } else {
                    $value = $this->getNodeValue($node);
                    array_push($tabTags, $value);
                }
            }
        }

        // add tags that are generated by shortcodes
        $tabTags = apply_filters('mazen_get_generated_html_in_shortcodes', $tabTags, $post, $tag);

        return $tabTags;
    }

    /**
     * @param $node
     * @param string $attr
     * @return mixed|string
     */
    public function getNodeValue($node, $attr = '')
    {
        if ($this->isDOMDocument == 1) {
            if ($attr == '') {
                $val = OptimizmeMazenUtils::safeUtf8Encode($node->nodeValue);
            } else {
                $val = OptimizmeMazenUtils::safeUtf8Encode($node->getAttribute($attr));
            }
        } else {
            $val = OptimizmeMazenDomParsing::getValueFromSHDPTag($node, $attr);
        }

        return $val;
    }

    /**
     * @param $node
     * @param string $attr
     * @return mixed
     */
    public static function getValueFromSHDPTag($node, $attr = '')
    {
        if ($attr == '') {
            // get value in node
            $value = $node->innertext();
        } else {
            // get attribute value
            $value = $node->{$attr};

            // if not set, the library returns a boolean false. Change for empty string
            if (!$value) {
                $value = '';
            }
        }

        // remove  \t \n...
        $value = OptimizmeMazenUtils::removeSpecialSeparatorChars($value);

        return $value;
    }

    /**
     * @return array
     */
    public function getNodesHtml()
    {
        $tabContent = [];
        foreach ($this->nodes as $node) {
            if ($this->isDOMDocument == 1) {
                array_push($tabContent, utf8_decode($this->doc->saveHTML($node)));
            } else {
                array_push($tabContent, 'tres'); // TODO why???
            }
        }
        return $tabContent;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        if ($this->isDOMDocument == 1) {
            $racine = $this->doc->getElementsByTagName('span')->item(0);
            $newContent = '';
            if ($racine->hasChildNodes()) {
                foreach ($racine->childNodes as $node) {
                    $newContent .= utf8_decode($this->doc->saveHTML($node));
                }
            }
        } else {
            $newContent = $this->dom;
        }

        return $newContent;
    }
}
