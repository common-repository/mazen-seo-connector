<?php
/**
 *
 * ex : [av_contact email='info@test.com' title='Send us mail' button='Submit' on_send='' sent='Your message has been sent!' link='manually,http://' subject='' autorespond='' captcha='active' form_align='' color='' admin_preview_bg=''] [av_contact_field label='Name' type='text' check='is_empty' options='' multi_select='' av_contact_preselect='' width=''][/av_contact_field] [av_contact_field label='E-Mail' type='text' check='is_email' options='' multi_select='' av_contact_preselect='' width=''][/av_contact_field] [av_contact_field label='Subject' type='text' check='is_empty' options='' multi_select='' av_contact_preselect='' width=''][/av_contact_field] [av_contact_field label='Message' type='textarea' check='is_empty' options='' multi_select='' av_contact_preselect='' width=''][/av_contact_field] [/av_contact]
 */

namespace Optimizme\Mazen\AviaFramework;

class av_contact extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h3'];
        $result = $this->getSimpleAviaBuilderShortcode($tag, $allowedTags, 'title');

        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $allowedTags = ['h3'];
        if ($this->attributes['title'] != '') {
            $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'title');
        } else {
            $result = [];
        }

        return $result;
    }
}
