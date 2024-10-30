<?php
/**
 * Heading
 *
 * Default: générate h2
 *
 * ex:
 * [ffb_heading_0 unique_id="1k37e09k" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22text-is-richtext%22%3A%220%22%2C%22tag%22%3A%22h2%22%2C%22align%22%3A%22text-center%22%2C%22align-sm%22%3A%22%22%2C%22align-md%22%3A%22%22%2C%22align-lg%22%3A%22%22%7D%7D%7D"][ffb_param route="o gen text"]Big Heading default[/ffb_param][/ffb_heading_0]
 */

namespace Optimizme\Mazen\FreshBuilder;

class ffb_one_quote_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
{
    public $child = '';

    /**
     * Extract generated HTML
     * @param $tag
     * @return string
     */
    public function get($tag)
    {
        //$data = $this->decodeFfbData($this->attributes['data']);
        //print_r($data); die;

        if (!parent::isBlockDisabled()) {
            // this shortcode can act on Hx

            $tagsHx = ['h3'];
            if (in_array($tag, $tagsHx)) {
                $blocks = $this->extractShortcodesInnerFfbParam(['quote text']);
                $result = $this->extractInnerFromShortcodes($blocks);
            }
        }

        // blocs html génériques
        //$result = parent::getHtmlGenerique($result, $tag);

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
            // this shortcode can act on Hx
            $tagsHx = ['h3'];
            if (in_array($tag, $tagsHx)) {
                // get ffb_param shortcodes inside main shortcode
                $blocks = $this->extractShortcodesInnerFfbParam(['quote text']);
                $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
            }
        }

        // blocs html génériques
        //$result = parent::setHtmlGenerique($result, $newDatas, $tag);

        return $result;
    }
}