<?php

namespace App\Shortcodes;

class BannerShortcode extends BaseShortCode
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
            ob_start();
            echo sprintf('<div class="sport-program"><div class="row"><div class="col-md-12">
					<div class="sport-section" style="background-image: url(%s);">
							<h2>%s</h2>
							<a href="%s" class="button-border">%s <i class="fa fa-angle-right" aria-hidden="true"></i></a>
						</div></div></div></div>'
                , $model->image, $model->title, $model->link, $model->button_text);

            return ob_get_clean();
        }
    }
}
