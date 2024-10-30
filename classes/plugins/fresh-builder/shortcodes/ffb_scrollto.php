<?php
/**
 * Scroll To
 *
 * ex:
 * [ffb_scrollto_0 unique_id=\"1mvli1qm\" data=\"%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22content%22%3A%7B%220-%7C-one-icon%22%3A%7B%22one-icon%22%3A%7B%22icon%22%3A%22ff-font-awesome4 icon-angle-double-down%22%7D%7D%2C%221-%7C-html%22%3A%7B%22html%22%3A%7B%22html%22%3A%7B%22html%22%3A%22%3Ch3%3EContenu h3 1%3C%2Fh3%3E%5Cn%3Ch3%3EContenu h3 2%3C%2Fh3%3E%22%2C%22html-is-richtext%22%3A%220%22%7D%7D%7D%2C%222-%7C-html%22%3A%7B%22html%22%3A%7B%22html%22%3A%7B%22html%22%3A%22%3Ch3%3EContenu h3 3%3C%2Fh3%3E%22%2C%22html-is-richtext%22%3A%220%22%7D%7D%7D%7D%2C%22link%22%3A%22%23%22%2C%22scrollto-anim%22%3A%22ffb-scrollto-anim-bounce%22%7D%7D%7D\"][/ffb_scrollto_0]
 */

namespace Optimizme\Mazen\FreshBuilder;

use Optimizme\Mazen\OptimizmeMazenDomParsing;

class ffb_scrollto_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
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
            // this shortcode can act on Hx
            $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (in_array($tag, $tagsHx)) {
                $data = $this->decodeFfbData($this->attributes['data']);
                $result = $this->getHtmlFromData($data->o->gen->content, $tag);
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
            // this shortcode can act on Hx
            $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (in_array($tag, $tagsHx)) {
                $mazenDom = new OptimizmeMazenDomParsing();
                $data = $this->decodeFfbData($this->attributes['data']);
                $result = $this->setHtmlFromData($mazenDom, $data->o->gen->content, $tag, $newDatas);
            }

            //echo "SET RESULT:";
            //print_r($result); die;
        }

        return $result;
    }
}

