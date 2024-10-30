<?php

namespace Optimizme\Mazen\VisualComposer;

class vc_hoverbox extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        // works only with h2 tag
        if ($tag == 'h2') {
            if (isset($this->attributes['primary_title'])) {
                $result[] = $this->attributes['primary_title'];
            }

            if (isset($this->attributes['hover_title'])) {
                $result[] = $this->attributes['hover_title'];
            } else {
                $result[] = 'Hover Box Element';
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
        $dataDone = [];

        if ($tag == 'h2') {
            if (isset($this->attributes['primary_title'])) {
                $boolNewContent = true;
                $this->attributes['primary_title'] = $newData;
                $dataDone[] = $newData;
                if (count($newDatas)>1) {
                    $newData = $newDatas[1];
                }
            }

            $boolNewContent = true;
            $this->attributes['hover_title'] = $newData;
            $dataDone[] = $newData;

            if ($boolNewContent == true) {
                $result = $this->returnDiffShortcodes($dataDone);
            }
        }

        return $result;
    }
}
