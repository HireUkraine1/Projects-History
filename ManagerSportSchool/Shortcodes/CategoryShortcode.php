<?php

namespace App\Shortcodes;

use App\Models;

class CategoryShortcode
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
        $categories = Models\Category::whereIn('slug', ['sport', 'sport', 'sport'])->get();
        if ($categories->count()) {
            ob_start();
            echo '<div class="row"><div class="col-xs-12">';
            foreach ($categories as $category) {
                echo sprintf('<div class="col-xs-4 sport category-wrap">
                    <a href="%s">
                        <div class="category-item">
                            <div class="img-holder">
                                <img src="%s" alt="%s">
                            </div>

                            <div style="background-image: url(%s);" class="category-title %s-title">
                                <h2>%s</h2>
                            </div>
                        </div>
                    </a>
                </div>', $category->slug, $category->thumbnail, $category->name, $category->lable, strtolower($category->slug), $category->alias, $category->description);
            }
            echo '</div></div>';
            return ob_get_clean();
        }
    }
}
