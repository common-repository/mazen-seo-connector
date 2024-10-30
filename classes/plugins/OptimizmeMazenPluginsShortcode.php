<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginsShortcode
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginsShortcode extends OptimizmeMazenPluginsBaseShortcode
{
    /**
     * @param $data (array with 'content' and 'tag')
     * @return array
     */
    public function extractHtmlFromShortcodes($data)
    {
        $res = [];
        extract($data);

        $keysShortcodes = $this->getShortcodesGeneratingTag($tag);
        if (!empty($keysShortcodes)) {
            // récupération des shortcodes présents dans le champ souhaité qui génèrent le tag souhaité
            $tabShortcodes = $this->getShortcodesInContent($content, $keysShortcodes);

            if (is_array($tabShortcodes) && !empty($tabShortcodes)) {
                foreach ($tabShortcodes as $objShortcode) {
                    $class = $this->namespace . $objShortcode->base;
                    $class = $this->removeSpecialChars($class);
                    if (method_exists($class, 'get')) {
                        $obj = new $class($objShortcode);
                        $data = $obj->get($tag);
                        if (is_array($data) && !empty($data)) {
                            $res = array_merge($res, $data);
                        }
                    }
                }
            }
        }

        return $res;
    }

    /**
     * @param $data : array containing 'content', 'tag' 'newDatas'
     * @return array
     */
    public function editShortcodesForHtmlTags($data)
    {
        $res = [];
        extract($data);

        if (!empty($newDatas)) {
            $keysShortcodes = $this->getShortcodesGeneratingTag($tag);

            if (!empty($keysShortcodes)) {
                // get all shortcodes which can generate the targeted tag in the content
                $tabShortcodes = $this->getShortcodesInContent($content, $keysShortcodes);

                if (is_array($tabShortcodes) && !empty($tabShortcodes)) {
                    foreach ($tabShortcodes as $objShortcode) {
                        if (is_array($newDatas) && !empty($newDatas)) {
                            $class = $this->namespace . $objShortcode->base;
                            $class = $this->removeSpecialChars($class);
                            if (method_exists($class, 'set')) {
                                $obj = new $class($objShortcode);
                                $data = $obj->set($tag, $newDatas);
                                if (is_array($data) && !empty($data)) {
                                    $res[] = $data;
                                    if (is_array($data['updated_values']) && !empty($data['updated_values'])) {
                                        $newDatas = array_slice($newDatas, count($data['updated_values']));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $res;
    }

    /**
     * @param $content
     * @param $keysShortcodes
     * @return array
     */
    public function getShortcodesInContent($content, $keysShortcodes)
    {
        $pattern = get_shortcode_regex($keysShortcodes);
        preg_match_all("/$pattern/", $content, $matches);

        $shortcodes = [];
        if (is_array($matches[2]) && !empty($matches[2])) {
            for ($i = 0; $i < count($matches[2]); $i++) {
                $tabShortcode = [
                    'base' => $matches[2][$i],
                    'complete' => $matches[0][$i],
                    'inner' => $matches[5][$i],
                    'attr' => $matches[3][$i]
                ];
                $objShortcode = new OptimizmeMazenPluginsBaseShortcode($tabShortcode);
                array_push(
                    $shortcodes,
                    $objShortcode
                );
            }
        }

        return $shortcodes;
    }

    /**
     * @param $post
     * @param $tag
     * @return array|void
     */
    public static function getDataInShortcode($post, $tag)
    {
        $data = [];
        $pageBuilders = OptimizmeMazenPluginsInteraction::getPageBuildersActivated();

        if (is_array($pageBuilders) && !empty($pageBuilders)) {
            foreach ($pageBuilders as $pageBuilder) {
                if ($pageBuilder == 'Elementor') {
                    $elementor = new OptimizmeMazenPluginElementor();
                    $data = $elementor->getTagsGeneratedByElement($post, $tag);
                } else {
                    $data = OptimizmeMazenPluginsInteraction::exectuteMethodFromPluginClass(
                        $pageBuilder,
                        'extractHtmlFromShortcodes',
                        ['content' => $post->post_content, 'tag' => $tag]
                    );
                }
            }
        }

        return $data;
    }

    /**
     * @param OptimizmeMazenDomParsing $mazenDom
     * @param $values
     * @param $content
     * @param $tag
     * @return string
     */
    public static function setShortcodesFromContent($mazenDom, array $values, $content, $tag)
    {
        $boolChange = false;

        if ($mazenDom->nbNodes < count($values)) {
            // we want to change more things that we have in the basic HTML
            // check for shortcodes
            $offset = $mazenDom->nbNodes - count($values);
            $valuesLeft = array_splice($values, $offset);
            $updatedShortcodes = OptimizmeMazenPluginsShortcode::setDataInShortcode($content, $tag, $valuesLeft);

            if (!empty($updatedShortcodes)) {
                $mazenDom->boolSave = 1;
                $boolChange = true;

                foreach ($updatedShortcodes as $updatedShortcode) {
                    // replace only in the first matching shortcode
                    $pos = strpos($content, $updatedShortcode['old']);
                    if ($pos !== false) {
                        $content = substr_replace($content, $updatedShortcode['new'], $pos, strlen($updatedShortcode['old']));
                    }

                    if (!empty($updatedShortcode['updated_values'])) {
                        foreach ($updatedShortcode['updated_values'] as $updated_value) {
                            array_push($mazenDom->tabSuccess, $updated_value);
                        }
                    }
                }
            }
        }


        if ($boolChange) {
            return $content;
        } else {
            return '';
        }
    }

    /**
     * @param $content
     * @param $tag
     * @return array
     */
    public static function setDataInShortcode($content, $tag, $newDatas)
    {
        $updatedShortcode = [];
        $pageBuilders = OptimizmeMazenPluginsInteraction::getPageBuildersActivated();
        if (is_array($pageBuilders) && !empty($pageBuilders)) {
            foreach ($pageBuilders as $pageBuilder) {
                $updatedShortcode = OptimizmeMazenPluginsInteraction::exectuteMethodFromPluginClass(
                    $pageBuilder,
                    'editShortcodesForHtmlTags',
                    ['content' => $content, 'tag' => $tag, 'newDatas' => $newDatas]
                );
            }
        }
        return $updatedShortcode;
    }

    /**
     * Remove some special characters
     * @param $name
     * @return mixed
     */
    public function removeSpecialChars($name)
    {
        $search = array('-');
        $replace = array('_');
        return str_replace($search, $replace, $name);
    }
}
