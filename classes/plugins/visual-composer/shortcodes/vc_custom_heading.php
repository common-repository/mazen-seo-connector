<?php

namespace Optimizme\Mazen\VisualComposer;

class vc_custom_heading extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        // this shortcode can act only on Hx
        if (isset($this->attributes['text'])) {
            if (!isset($this->attributes['font_container']))
                $this->attributes['font_container'] = 'tag:h2';

            if (strstr($this->attributes['font_container'], 'tag:' . $tag)) {
                $result[] = $this->attributes['text'];
            }
        }

        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $result = [];
        $boolNewContent = false;
        $newData = $newDatas[0];

        // this shortcode can act on Hx
        $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        if (in_array($tag, $tagsHx)) {
            if (isset($this->attributes['text'])) {
                if (!isset($this->attributes['font_container']))
                    $this->attributes['font_container'] = 'tag:h2';

                if (strstr($this->attributes['font_container'], 'tag:' . $tag)) {
                    $boolNewContent = true;
                }
            }
        }

        if ($boolNewContent == true) {
            // change data
            $this->attributes['text'] = $newData;
            $result = $this->returnDiffShortcodes([$newData]);
        }

        return $result;
    }
}
