<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginFreshBuilder
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginFreshBuilder extends OptimizmeMazenPluginsShortcode
{
    public $saveInWpContent = 1;
    public $namespace = "\Optimizme\Mazen\FreshBuilder\\";

    /**
     * @param $result
     * @param $tag
     * @return array
     */
    public function getHtmlGenerique($result, $tag)
    {
        $data = $this->attributes['data'];
        $data = $this->decodeFfbData($data);
        $res = $this->getHtmlFromData($data->o->gen->content, $tag);

        if (is_array($res) && !empty($res)) {
            if (!is_array($result)) {
                $result = [];
            }
            $result = array_merge($result, $res);
        }
        return $result;
    }

    /**
     * @param $result
     * @param $newDatas
     * @param $tag
     * @return array
     */
    public function setHtmlGenerique($result, $newDatas, $tag)
    {
        $mazenDom = new OptimizmeMazenDomParsing();
        $data = $this->decodeFfbData($this->attributes['data']);
        $res = $this->setHtmlFromData($mazenDom, $data->o->gen->content, $tag, $newDatas);

        if (!empty($res)) {
            if (!is_array($result)) {
                $result = [];
            }

            $result = array_merge($result, $res);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isBlockDisabled()
    {
        $data = $this->decodeFfbData($this->attributes['data']);
        if (isset($data->o->gen)) {
            $arr = json_decode(json_encode($data->o->gen), true);
            if ($arr['ffsys-disabled'] == 1)
                return true;
        }

        return false;
    }

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
                'ffb_accordion-1_0',
                'ffb_download_0',
                'ffb_embeddedCode_0',
                'ffb_faq_0',
                'ffb_heading_0', 'ffb_heading-1_0', 'ffb_heading-1_2', 'ffb_heading_2', 'ffb_heading-2_0', 'ffb_heading-3_0', 'ffb_heading-4_0',
                'ffb_html_0', 'ffb_html_2', 'ffb_html_3',
                //'ffb_list_0',
                'ffb_iconbox-1_0', 'ffb_iconbox-2_0', 'ffb_iconbox-2_3', 'ffb_iconbox-3_0', 'ffb_iconbox-4_0', 'ffb_iconbox-5_0', 'ffb_iconbox-6_0', 'ffb_iconbox-7_0', 'ffb_iconbox-8_0', 'ffb_iconbox-10_0', 'ffb_iconbox-12_0',
                'ffb_interactive-banner-1_0',
                'ffb_one-quote_0',
                'ffb_scrollto_0',
                'ffb_section-heading_0', 'ffb_section-heading_2',
                'ffb_services-1_0', 'ffb_services-3_0', 'ffb_services-4_0', 'ffb_services-5_0', 'ffb_services-6_0', 'ffb_services-7_0', 'ffb_services-9_0', 'ffb_services-10_0', 'ffb_services-11_0', 'ffb_services-12_0', 'ffb_services-13_0', 'ffb_services-14_0', 'ffb_services-15_0',
                'ffb_team-1_0', 'ffb_team-1_2',  'ffb_team-2_0', 'ffb_team-2_4','ffb_team-3_0', 'ffb_team-4_0', 'ffb_team-5_0', 'ffb_team-6_0', 'ffb_team-7_0', 'ffb_team-7_2', 'ffb_team-8_0', 'ffb_team-8_2', 'ffb_team-9_0', 'ffb_team-10_0', 'ffb_team-11_0', 'ffb_team-12_0', 'ffb_team-13_0',
                'ffb_testimonials1_0', 'ffb_testimonials2_0', 'ffb_testimonials3_0', 'ffb_testimonials4_0', 'ffb_testimonials5_0', 'ffb_testimonials6_0', 'ffb_testimonials7_0', 'ffb_testimonials-8_0', 'ffb_testimonials9_0',
                'ffb_work_2',
            ];
        }

        return $tabShortcodes;
    }

    /**
     * @param array $tags
     * @return array
     */
    public function extractShortcodesInnerFfbParam($tags = array())
    {
        $tabReturn = [];

        $shortcodes = $this->getShortcodesInContent($this->inner, ['ffb_param']);
        if (is_array($shortcodes) && !empty($shortcodes)) {
            if (empty($tags)) {
                // no filter: return all param blocks
                $tabReturn = $shortcodes;
            } else {
                // for each ffb_param, test if 'route' contains one of the wanted tags
                foreach ($shortcodes as $shortcode) {
                    foreach ($tags as $tag) {
                        if ($this->ffbParamHasTagInAttributes($shortcode, $tag)) {
                            $tabReturn[] = $shortcode;
                        }
                    }
                }
            }
        }

        return $tabReturn;
    }

    /**
     * @param array $shortcodes
     * @return array
     */
    public function extractInnerFromShortcodes(array $shortcodes)
    {
        $tab = [];
        if (is_array($shortcodes) && !empty($shortcodes)) {
            foreach ($shortcodes as $shortcode) {
                $tab[] = $shortcode->inner;
            }
        }
        return $tab;
    }

    /**
     * @return mixed
     */
    public function getFreshBuilderInnerContent()
    {
        $sh = $this->getShortcodesInContent($this->inner, ['ffb_param']);
        $sh2 = new OptimizmeMazenPluginsShortcode($sh[0]);
        $result = $sh2->inner;
        return $result;
    }

    /**
     * @param $shortcode
     * @param $tag
     * @return bool
     */
    public function ffbParamHasTagInAttributes($shortcode, $tag)
    {
        if (isset($shortcode->attributes) && isset($shortcode->attributes['route'])) {
            $tabClasses = explode(' ', $shortcode->attributes['route']);

            if (strstr($tag, ' ')) {
                $tabTags = explode(' ', $tag);
            } else {
                $tabTags = [$tag];
            }

            foreach ($tabTags as $tagLoop) {
                if (!in_array($tagLoop, $tabClasses)) {
                    return false;
                }
            }

        }
        return true;
    }

    /**
     * @param array $shortcodes
     * @param array $newDatas
     * @return array
     */
    public function setShortcodesInnerFfbParam(array $shortcodes, array $newDatas)
    {
        $result = [];
        $res = [];
        $tabInner = [];
        $newContent = $this->inner;

        // for each "ffb_param" inner shortcode
        if (is_array($shortcodes) && !empty($shortcodes)) {
            foreach ($shortcodes as $shortcode) {
                if (is_array($newDatas) && !empty($newDatas)) {
                    // update this "ffb_param" inner data
                    $res[] = $newDatas[0];
                    $shortcode->inner = $newDatas[0];
                    $tabInner[] = [
                        'updated_values' => $newDatas[0],
                        'old' => $shortcode->complete,
                        'new' => $shortcode->regenerateShortcode()
                    ];
                    array_shift($newDatas);
                }
            }
        }

        if (!empty($tabInner)) {
            // some inner "ffb_param" have changed
            foreach ($tabInner as $updatedShortcode) {
                // replace only in the first matching shortcode in content
                $pos = strpos($newContent, $updatedShortcode['old']);
                if ($pos !== false) {
                    $newContent = substr_replace($newContent, $updatedShortcode['new'], $pos, strlen($updatedShortcode['old']));
                }
            }

            // update main shortcode content
            $this->inner = $newContent;

            // return diff shortcode
            $result = $this->returnDiffShortcodes($res);
        }
        return $result;
    }

    /**
     * @param $data
     * @return string
     */
    public function encodeFfbData($data)
    {
        $json = json_encode($data);
        if (!is_string($json)) {
            return false;
        } else {
            return urlencode($json);
        }
    }

    /**
     * @param $data
     * @return array|mixed|object
     */
    public function decodeFfbData($data)
    {
        return json_decode(urldecode($data));
    }

    /**
     * @param $data
     * @param $tag
     * @return array
     */
    public function getHtmlFromData($data, $tag)
    {
        $res = [];
        if (!empty($data)) {
            foreach ($data as $key => $loop) {
                $tabKey = explode('-|-', $key);

                if (is_array($tabKey) && isset($tabKey[1]) && $tabKey[1] == 'html') {
                    $html = $loop->html->html->html;
                    $mazenDom = new \Optimizme\Mazen\OptimizmeMazenDomParsing();
                    $mazenDom->getNodesInContent($html, $tag);
                    $nodes = $mazenDom->getNodesValues();
                    $res = array_merge($res, $nodes);
                }
            }
        }

        return $res;
    }

    /**
     * @param OptimizmeMazenDomParsing $mazenDom
     * @param $data
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function setHtmlFromData(OptimizmeMazenDomParsing $mazenDom, $data, $tag, $newDatas)
    {
        $result = [];

        // change all values in matching tags
        if (!empty($data)) {
            foreach ($data as $key => $loop) {
                $tabKey = explode('-|-', $key);
                if (is_array($tabKey) && isset($tabKey[1]) && $tabKey[1] == 'html') {
                    $html = $loop->html->html->html;
                    $mazenDom->getNodesInContent($html, $tag);
                    if ($mazenDom->nbNodes > 0) {
                        $newDatas = $mazenDom->changeNodesValues($newDatas, '', 1);
                        $data->$key->html->html->html = $mazenDom->getHtml();
                    }
                }
            }
        }

        // if values have changed
        if (!empty($mazenDom->tabSuccess)) {
            $allData = $this->decodeFfbData($this->attributes['data']);
            $allData->o->gen->content = $data;
            $encodedData = $this->encodeFfbData($allData);
            if ($encodedData !== false) {
                $this->attributes['data'] = $encodedData;
                $result = $this->returnDiffShortcodes($mazenDom->tabSuccess);
            }
        }

        return $result;
    }
}
