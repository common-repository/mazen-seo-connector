<?php

namespace Optimizme\Mazen\VisualComposer;

class vc_toggle extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        if (isset($this->attributes['title'])) {
            if (!isset($this->attributes['custom_font_container']))
                $this->attributes['custom_font_container'] = 'tag:h4';

            if (strstr($this->attributes['custom_font_container'], 'tag:' . $tag)) {
                $result[] = $this->attributes['title'];
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

        if (isset($this->attributes['title'])) {

            if (!isset($this->attributes['custom_font_container']))
                $this->attributes['custom_font_container'] = 'tag:h4';

            if (strstr($this->attributes['custom_font_container'], 'tag:' . $tag)) {
                $boolNewContent = true;
            }
        }

        if ($boolNewContent == true) {
            $this->attributes['title'] = $newData;
            $result = $this->returnDiffShortcodes([$newData]);
        }

        return $result;
    }
}
