<?php

namespace Optimizme\Mazen\VisualComposer;

class vc_text_separator extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        // works only with h4 tag
        $result = $this->getSimpleAttrInVcShortcode($tag,  ['h4'], 'title');
        return $result;
    }

    /**
     * @param $tag
     * @param array $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $newData = $newDatas[0];
        $result = $this->setSimpleAttrInVcShortcode($tag,  ['h4'], 'title', $newData);
        return $result;
    }
}