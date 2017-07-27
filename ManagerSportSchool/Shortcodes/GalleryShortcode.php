<?php

namespace App\Shortcodes;

class GalleryShortcode extends BaseShortCode
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
            $gallery = json_decode($model->blocks);
            ob_start();
            if ($model->description == 'Yes') {
                echo '<div class="category-instructor"><div class="row">';
                foreach ($gallery as $element) {
                    echo sprintf('<div class="col-sm-4 col-xs-6 instructors-wrapper"><a href="%s"><div class="course-item ">', $element->url);
                    echo sprintf('<div class="course-img"><img src="%s" alt="%s"></div>', $element->src, $element->title);
                    echo sprintf('<div class="course-description"><h5>%s</h5><p>%s</p></div>', $element->title, $element->description);
                    echo '</div></a></div>';
                }
                echo '</div></div>';
            } else {
                echo '<div class="all-products"> <div class="row">';
                echo sprintf('<div class="col-md-12"> <h3 class="text-center">%s</h3></div>', $model->name);
                foreach ($gallery as $element) {
                    echo sprintf('<div class="col-sm-4 col-xs-6 products-item"><a href="%s"><div class="products-wrap" style="background-image: url(%s);">
                        <h4>%s</h4></div></a></div>', $element->url, $element->src, $element->title);
                }
                echo '</div></div>';
            }
            return ob_get_clean();
        }
    }

}
