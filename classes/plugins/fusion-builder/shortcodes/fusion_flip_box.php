<?php

namespace Optimizme\Mazen\FusionBuilder;

class fusion_flip_box extends \Optimizme\Mazen\OptimizmeMazenPluginFusionBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        // head front : h2
        // head back : h3
        $result = [];

        if ($tag == 'h2') {
            $result[] = $this->attributes['title_front'];
        } elseif($tag == 'h3') {
            $result[] = $this->attributes['title_back'];
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
        $isChanged = 0;
        $newData = $newDatas[0];

        if ($tag == 'h2') {
            $this->attributes['title_front'] = $newData;
            $isChanged = 1;
        } elseif($tag == 'h3') {
            $this->attributes['title_back'] = $newData;
            $isChanged = 1;
        }

        if ($isChanged == 1) {
            $result = $this->returnDiffShortcodes([$newData]);
        }

        return $result;
    }
}
