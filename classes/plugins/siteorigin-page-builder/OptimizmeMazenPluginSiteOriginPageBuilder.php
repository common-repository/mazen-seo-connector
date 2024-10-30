<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginSiteOriginPageBuilder
 */
class OptimizmeMazenPluginSiteOriginPageBuilder
{
    public $saveInWpContent = 1;
    public $namespace = "\Optimizme\Mazen\SiteOriginPageBuilder\\";

    public $tag;
    public $attr;
    public $tabValues;
    public $postmetaKeySiteOriginPageBuilder;
    public $tagDefaultWidgetTitle;
    public $cpt;

    /**
     * OptimizmeMazenPluginElementor constructor.
     */
    public function __construct()
    {
        $this->postmetaKeySiteOriginPageBuilder = 'panels_data';
        $this->tagDefaultWidgetTitle = 'h3';
        $this->cpt = 0;
    }

    /**
     * @param $post
     * @param $tabData
     */
    public function save($post, $tabData)
    {
        if (isset($tabData['tag'])) {
            $tabHn = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (in_array($tabData['tag'], $tabHn)) {
                $this->changeTagInContent($post, $tabData);
            }
        }
    }

    /**
     * @param $post
     * @param $tabData
     */
    public function changeTagInContent($post, $tabData)
    {
        $this->tag = $tabData['tag'];
        $this->tabValues = $tabData['values'];
        $objSiteOrigin = get_post_meta($post->ID, $this->postmetaKeySiteOriginPageBuilder, true);

        if (is_array($objSiteOrigin) && !empty($objSiteOrigin) && isset($objSiteOrigin['widgets']) && !empty($objSiteOrigin['widgets'])) {
            foreach ($objSiteOrigin['widgets'] as $widget) {
                if (isset($widget['title']) && $widget['title'] != '') {
                    if ($this->tag == $this->tagDefaultWidgetTitle) {
                        // I want to edit a Hn which is used by default in widget title
                        $widget = $this->updateWidgetTitle($widget);
                    }
                }

                // change in widget text content
                $widget = $this->updateInDom($widget);
                $objSiteOrigin['widgets'][$this->cpt] = $widget;

                $this->cpt++;
            }
        }

        update_metadata(
            'post',
            $post->ID,
            $this->postmetaKeySiteOriginPageBuilder,
            map_deep($objSiteOrigin, ['SiteOrigin_Panels_Admin', 'double_slash_string'])
        );
    }

    /**
     * @param $widget
     * @return mixed
     */
    public function updateWidgetTitle($widget)
    {
        $widget['title'] = $this->tabValues[0];
        unset($this->tabValues[0]);
        $this->tabValues = array_values($this->tabValues);

        return $widget;
    }

    /**
     * @param $widget
     * @return mixed
     */
    public function updateInDom($widget)
    {
        $content = $widget['text'];

        $mazenDom = new OptimizmeMazenDomParsing();
        $mazenDom->getNodesInContent($content, $this->tag);
        $valuesLeft = $mazenDom->changeNodesValues($this->tabValues, $this->attr, 1);
        $widget['text'] = $mazenDom->getHtml();
        $this->tabValues = $valuesLeft;

        return $widget;
    }
}
