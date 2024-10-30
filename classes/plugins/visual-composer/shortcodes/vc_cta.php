<?php

namespace Optimizme\Mazen\VisualComposer;

class vc_cta extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        // heading in h2, subheading in h4
        if ($tag == 'h2') {
            if (isset($this->attributes['h2']))
                $result = $this->getSimpleAttrInVcShortcode($tag,  ['h2'], 'h2');
            else
                $result[] = 'Hey! I am first heading line feel free to change me';
        } elseif ($tag == 'h4') {
            $result = $this->getSimpleAttrInVcShortcode($tag,  ['h4'], 'h4');
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
        $newData = $newDatas[0];

        // heading in h2, subheading in h4
        if ($tag == 'h2') {
            $result = $this->setSimpleAttrInVcShortcode($tag,  ['h2'], 'h2', $newData, 1);
        } elseif ($tag == 'h4') {
            $result = $this->setSimpleAttrInVcShortcode($tag,  ['h4'], 'h4', $newData);
        }

        return $result;
    }
}
