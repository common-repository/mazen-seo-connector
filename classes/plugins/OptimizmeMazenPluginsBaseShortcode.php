<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginsShortcode
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginsBaseShortcode
{
    public $base;
    public $complete;
    public $inner;
    public $attributes;

    /**
     * OptimizmeMazenPluginsBaseShortcode constructor.
     * @param string $shortcode
     */
    public function __construct($shortcode = '')
    {
        if (is_array($shortcode)) {
            $this->base = $shortcode['base'];
            $this->complete = $shortcode['complete'];
            $this->inner = $shortcode['inner'];
            $this->attributes = shortcode_parse_atts($shortcode['attr']);
        }

        if (is_object($shortcode)) {
            $this->base = $shortcode->base;
            $this->complete = $shortcode->complete;
            $this->inner = $shortcode->inner;
            $this->attributes = $shortcode->attributes;
        }
    }

    /**
     * @param array $tabNewContent
     * @return array
     */
    public function returnDiffShortcodes($tabNewContent)
    {
        if (!is_array($tabNewContent)) {
            $tabNewContent = [$tabNewContent];
        }

        return [
            'updated_values' => $tabNewContent,
            'old' => $this->complete,
            'new' => $this->regenerateShortcode()
        ];
    }

    /**
     * @return string
     */
    public function regenerateShortcode()
    {
        $sc = '[' . $this->base;
        if (is_array($this->attributes) && !empty($this->attributes)) {
            foreach ($this->attributes as $attribute => $value) {
                $sc .= ' ' . $attribute . '="' . $value . '"';
            }
        }
        $sc .= ']';

        if ($this->inner != '' || strstr($this->complete, '[/'. $this->base .']')) {
            $sc .= $this->inner;
            $sc .= '[/' . $this->base . ']';
        }
        return $sc;
    }
}
