<?php

namespace Optimizme\Mazen\VisualComposer;

class vc_video extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        // works only with h2 tag
        $result = $this->getSimpleAttrInVcShortcode($tag,  ['h2'], 'title');
        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $newData = $newDatas[0];
        $result = $this->setSimpleAttrInVcShortcode($tag,  ['h2'], 'title', $newData);
        return $result;
    }
}
