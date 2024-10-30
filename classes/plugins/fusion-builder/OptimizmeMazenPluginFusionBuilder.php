<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginFreshBuilder
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginFusionBuilder extends OptimizmeMazenPluginsShortcode
{
    public $saveInWpContent = 1;
    public $namespace = "\Optimizme\Mazen\FusionBuilder\\";

    /**
     * List of shortcodes that could generate this specified tag
     * @param $tag
     * @return array
     */
    public function getShortcodesGeneratingTag($tag)
    {
        $tabShortcodes = [];
        if ($tag == 'h1' || $tag == 'h2' || $tag == 'h3' || $tag == 'h4' || $tag == 'h5' || $tag == 'h6') {
            $tabShortcodes = [
                'fusion_title',
                'fusion_code',
                'fusion_content_box',
                'fusion_flip_box',
                'fusion_modal',
                'fusion_pricing_column',
                'fusion_sharing',
                'fusion_tab',
                'fusion_tagline_box',
                'fusion_toggle',
                'fusion_login',
                'fusion_lost_password',
                'fusion_register',
            ];
        }

        return $tabShortcodes;
    }

    /**
     * @param $tag
     * @param array $tagsAllowed
     * @param string $attr
     * @param int $noEmpty
     * @return array
     */
    public function getSimpleInnerFusionBuilderShortcode($tag, array $tagsAllowed, $attr = '', $noEmpty = 0)
    {
        $result = [];
        if (in_array($tag, $tagsAllowed)) {

            if (!empty($attr)) {
                if (isset($this->attributes[$attr])) {
                    if ($noEmpty == 0 || $this->attributes[$attr] != '')
                        $result[] = $this->attributes[$attr];
                }
            } else {
                // get inner
                if (isset($this->inner)) {
                    if ($noEmpty == 0 || $this->inner != '')
                        $result[] = $this->inner;
                }
            }
        }

        return $result;
    }

    /**
     * @param $tag
     * @param array $tagsAllowed
     * @param $newData
     * @param $attr
     * @return array
     */
    public function setSimpleInnerFusionBuilderShortcode($tag, array $tagsAllowed, $newData, $attr = '', $noEmpty = 0)
    {
        $result = [];
        $isChanged = 0;

        if (in_array($tag, $tagsAllowed)) {
            if ($attr != '') {
                if ($noEmpty == 0 || $this->attributes[$attr] != '') {
                    $isChanged = 1;
                    $this->attributes[$attr] = $newData;
                }
            } else {
                if ($noEmpty == 0 || $this->inner != '') {
                    $isChanged = 1;
                    $this->inner = $newData;
                }
            }

            if ($isChanged == 1) {
                $result = $this->returnDiffShortcodes([$newData]);
            }
        }


        return $result;
    }

}
