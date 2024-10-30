<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginElementor
 */
class OptimizmeMazenPluginElementor
{
    public $saveInWpContent = 0;
    public $namespace = "\Optimizme\Mazen\Elementor\\";

    public $tag;
    public $attr;
    public $tabValues;
    public $elementorDb;

    /**
     * OptimizmeMazenPluginElementor constructor.
     */
    public function __construct()
    {
        $this->elementorDb = new \Elementor\DB();
    }

    /**
     * @param $post
     * @param $tabData
     */
    public function save($post, $tabData)
    {
        if ($this->elementorDb->is_built_with_elementor($post->ID)) {
            if (isset($tabData['tag'])) {
                $tabHn = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
                if (in_array($tabData['tag'], $tabHn)) {
                    $this->changeTagInContent($post, $tabData);
                }
            }
        }
    }

    /**
     * @param $post
     * @param $tag
     * @return array
     */
    public function getTagsGeneratedByElement($post, $tag)
    {
        $plainEditor = $this->elementorDb->get_plain_editor($post->ID);
        $this->tag = $tag;
        $this->tabValues = [];

        $this->elementorDb->iterate_data($plainEditor, function ($element) {
            if (isset($element['settings']) && is_array($element['settings'])) {
                if (isset($element['widgetType'])) {
                    $widgetType = $element['widgetType'];
                } else {
                    $widgetType = '';
                }

                if (isset($element['settings']) && is_array($element['settings'])) {
                    if (strstr($widgetType, 'wp-widget-')) {
                        // all wp features have a title wrapped in h5
                        if ($this->tag == 'h5') {
                            $this->tabValues[]  = $element['settings']['wp']['title'];
                        }
                    }
                }
            }
        });

        return $this->tabValues;

    }

    /**
     * @param $post
     * @param $tabData
     */
    public function changeTagInContent($post, $tabData)
    {
        $plainEditor = $this->elementorDb->get_plain_editor($post->ID);
        $this->tabValues = $tabData['values'];
        $this->tag = $tabData['tag'];
        if (isset($tabData['attr'])) {
            $this->attr = '';
        }

        $data = $this->elementorDb->iterate_data($plainEditor, function ($element) {
            if (is_array($this->tabValues) && !empty($this->tabValues)) {
                if (isset($element['settings']) && is_array($element['settings'])) {
                    if (isset($element['widgetType'])) {
                        $widgetType = $element['widgetType'];
                    } else {
                        $widgetType = '';
                    }

                    if (isset($element['settings']) && is_array($element['settings'])) {
                        if ($widgetType == 'heading') {
                            $element = $this->updateSingleField($element, 'title', 'header_size', 'h2');
                        } elseif ($widgetType == 'text-editor') {
                            $element = $this->updateInDom($element, 'editor');
                        } elseif ($widgetType == 'html') {
                            $element = $this->updateInDom($element, 'html');
                        } elseif ($widgetType == 'icon-box') {
                            $element = $this->updateSingleField($element, 'title_text', 'title_size', 'h3');
                            $element = $this->updateInDom($element, 'description_text');
                        } elseif ($widgetType == 'image-box') {
                            $element = $this->updateSingleField($element, 'title_text', 'title_size', 'h3');
                            $element = $this->updateInDom($element, 'description_text');
                        } elseif ($widgetType == 'accordion' || $widgetType == 'tabs' || $widgetType == 'toggle') {
                            foreach ($element['settings']['tabs'] as $key => $tab) {
                                $element = $this->updateInDom($element, ['tabs', $key, 'tab_content']);
                            }
                        } elseif ($widgetType == 'testimonial') {
                            $element = $this->updateInDom($element, 'testimonial_content');
                        } elseif ($widgetType == 'alert') {
                            $element = $this->updateInDom($element, 'alert_description');
                        } elseif (strstr($widgetType, 'wp-widget-')) {
                            $element = $this->updateSingleField($element, ['wp', 'title'], '', 'h5');
                        }
                    }
                }
            }

            return $element;
        });

        $this->elementorDb->save_editor($post->ID, $data);
    }

    /**
     * @param $element
     * @param $elementorSettingTag
     * @param $settingSize
     * @param $default
     * @return mixed
     */
    public function updateSingleField($element, $elementorSettingTag, $settingSize, $default)
    {
        if (!is_array($elementorSettingTag)) {
            $elementorSettingTag = [$elementorSettingTag];
        }

        if (!isset($element['settings'][$settingSize])) {
            $tagInEditor = $default;
        } else {
            $tagInEditor = $element['settings'][$settingSize];
        }

        if ($tagInEditor == $this->tag) {
            // tags match
            if (count($elementorSettingTag) == 1)
                $element['settings'][$elementorSettingTag[0]] = $this->tabValues[0];
            elseif (count($elementorSettingTag) == 2) {
                $element['settings'][$elementorSettingTag[0]][$elementorSettingTag[1]] = $this->tabValues[0];
            }
            unset($this->tabValues[0]);
            $this->tabValues = array_values($this->tabValues);
        }
        return $element;
    }

    /**
     * @param $element
     * @param $elementorSettingTag
     * @return mixed
     */
    public function updateInDom($element, $elementorSettingTag)
    {
        $content = $element['settings'];

        // if not array
        if (!is_array($elementorSettingTag)) {
            $elementorSettingTag = [$elementorSettingTag];
        }

        // construct if array
        foreach ($elementorSettingTag as $elementArray) {
            $content = $content[$elementArray];
        }

        $mazenDom = new OptimizmeMazenDomParsing();
        $mazenDom->getNodesInContent($content, $this->tag);
        $valuesLeft = $mazenDom->changeNodesValues($this->tabValues, $this->attr, 1);

        $countElementsSettings = count($elementorSettingTag);
        if ($countElementsSettings == 1) {
            $element['settings'][$elementorSettingTag[0]] = $mazenDom->getHtml();
        } elseif ($countElementsSettings == 2) {
            $element['settings'][$elementorSettingTag[0]][$elementorSettingTag[1]] = $mazenDom->getHtml();
        } elseif ($countElementsSettings == 3) {
            $element['settings'][$elementorSettingTag[0]][$elementorSettingTag[1]][$elementorSettingTag[2]] = $mazenDom->getHtml();
        }

        //$element['settings'][$elementorSettingTag] = $mazenDom->getHtml();
        $this->tabValues = $valuesLeft;

        return $element;
    }
}
