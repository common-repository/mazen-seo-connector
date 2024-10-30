<?php
/**
 * Html and PHP Code
 *
 * ex:
 * [ffb_html_0 unique_id="1k39g2d1" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22html%22%3A%7B%22wrapper%22%3A%22div%22%2C%22html%22%3A%22%3Ch2%3ETest%3C%2Fh2%3E%22%2C%22html-is-richtext%22%3A%220%22%2C%22use-as-php%22%3A%221%22%7D%2C%22blank%22%3A%22null%22%7D%7D%7D"][/ffb_html_0]
 */

namespace Optimizme\Mazen\FreshBuilder;

use Optimizme\Mazen\OptimizmeMazenDomParsing;

class ffb_html_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        if (!parent::isBlockDisabled()) {
            $mazenDom = $this->extractFromHtmlCode($tag);
            if ($mazenDom->nbNodes > 0) {
                foreach ($mazenDom->nodes as $node) {
                    $result[] = $mazenDom->getNodeValue($node);
                }
            }
        }

        return $result;
    }

    /**
     * @param $tag
     * @param array $newDatas
     * @return array
     */
    public function set($tag, array $newDatas)
    {
        $result = [];

        if (!parent::isBlockDisabled()) {
            $mazenDom = $this->extractFromHtmlCode($tag);
            if ($mazenDom->nbNodes > 0) {
                $mazenDom->changeNodesValues($newDatas);
                $newContent = $mazenDom->getHtml();
            }

            if (!empty($mazenDom->tabSuccess)) {
                // data changed
                $data = $this->decodeFfbData($this->attributes['data']);
                $data->o->gen->html->html = $newContent;
                $data = $this->encodeFfbData($data);
                if ($data !== false) {
                    $this->attributes['data'] = $data;
                    $result = $this->returnDiffShortcodes($mazenDom->tabSuccess);
                }
            }
        }

        return $result;
    }


    /**
     * @param $tag
     * @return OptimizmeMazenDomParsing
     */
    public function extractFromHtmlCode($tag)
    {
        $mazenDom = new OptimizmeMazenDomParsing();

        // this shortcode can act on Hx
        $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        if (in_array($tag, $tagsHx)) {
            if (isset($this->attributes['data'])) {
                $data = $this->decodeFfbData($this->attributes['data']);

                if (isset($data->o) && isset($data->o->gen) && isset($data->o->gen->html) && isset($data->o->gen->html->html)) {
                    $code = $data->o->gen->html->html;
                    $mazenDom->getNodesInContent($code, $tag);
                }
            }
        }
        return $mazenDom;
    }
}

class ffb_html_2 extends ffb_html_0
{

}

class ffb_html_3 extends ffb_html_0
{

}