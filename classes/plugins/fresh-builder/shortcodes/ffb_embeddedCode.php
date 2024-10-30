<?php
/**
 * Embed code
 *
 * Custom code is in data attribute, urlencode and json_encode
 *
 * ex:
 * [ffb_embeddedCode_0 unique_id="1k31sfbh" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22iframe%22%3A%22%3Ch2%3ESALUT %3C%2Fh2%3E%5Cn%3Ch2%3ESALUT 2%3C%2Fh2%3E%22%2C%22width%22%3A%2216%22%2C%22height%22%3A%229%22%7D%7D%7D"][/ffb_embeddedCode_0]
 */

namespace Optimizme\Mazen\FreshBuilder;

use Optimizme\Mazen\OptimizmeMazenDomParsing;

class ffb_embeddedCode_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
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
            $mazenDom = $this->extractFromEmbeddedCode($tag);
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
            $mazenDom = $this->extractFromEmbeddedCode($tag);
            if ($mazenDom->nbNodes > 0) {
                $mazenDom->changeNodesValues($newDatas);
                $newContent = $mazenDom->getHtml();
            }

            if (!empty($mazenDom->tabSuccess)) {
                // data changed
                $data = $this->decodeFfbData($this->attributes['data']);
                $data->o->gen->iframe = $newContent;
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
    public function extractFromEmbeddedCode($tag)
    {
        $mazenDom = new OptimizmeMazenDomParsing();

        // this shortcode can act on Hx
        $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        if (in_array($tag, $tagsHx)) {
            if (isset($this->attributes['data'])) {
                $data = $this->decodeFfbData($this->attributes['data']);
                if (isset($data->o) && isset($data->o->gen) && isset($data->o->gen->iframe)) {
                    $code = $data->o->gen->iframe;
                    $mazenDom->getNodesInContent($code, $tag);
                }
            }
        }

        return $mazenDom;
    }
}
