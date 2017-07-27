<?php

namespace App\Shortcodes;

class SliderShortcode extends BaseShortCode
{
    /**
     * @param $shortcode
     * @param $content
     * @param $compiler
     * @param $name
     * @return string
     */
    public function register($shortcode, $content, $compiler, $name)
    {
        if ($model = $this->getModelorNull($shortcode, $name)) {
            $slider = json_decode($model->blocks);
            ob_start();
            echo '<div class="sport-course-programs"><div class="row"><div class="col-xs-12"><div class="courses-slider">';
            foreach ($slider as $slide) {
                echo '<div class="course-wrapper"><div class="course-item">';
                echo sprintf('<div class="course-img"><img src="%s" alt="%s"></div><div class="course-description"><h5>%s</h5><p>%s</p></div><div class="button-wrap"><a href="%s" class="bt-enrol">Enroll</a></div>', $slide->src, $slide->title, $slide->title, $slide->description, $slide->url);
                echo '</div></div>';
            }
            echo '</div></div></div></div>';
            return ob_get_clean();
        }
    }
}
