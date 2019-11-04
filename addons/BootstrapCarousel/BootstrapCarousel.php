<?php

class BootstrapCarousel extends \core\addons\XWAddonImplementation{
    private function renderIndicators($images, $rnd = 0){
        $result = '<ol class="carousel-indicators">';
        foreach ($images as $key => $image){
            if($key == 0){
                $result .= '<li data-target="#bs-carousel-generic_' . $rnd . '" data-slide-to="' . $key . '" class="active"></li>';
            }
            else{
                $result .= '<li data-target="#bs-carousel-generic_' . $rnd . '" data-slide-to="' . $key . '"></li>';
            }
        }
        $result .= '</ol>';
        return $result;
    }

    private function renderSlides($images){
        $result = '<div class="carousel-inner" role="listbox">';
        foreach ($images as $key => $image){
            $url = null;
            $caption = '';
            if(is_array($image)){
                $url = $image['src'];
                $caption = $image['caption']; //can contain HTML-Code!
            }
            else{
                $url = $image;
            }

            if($url){
                if($key == 0){
                    $result .= '
                        <div class="item active">
                          <img src="' . $url . '" alt="">
                          <div class="carousel-caption">' . $caption . '</div>
                        </div>
                    ';
                }
                else{
                    $result .= '
                        <div class="item">
                          <img src="' . $url . '" alt="">
                          <div class="carousel-caption">' . $caption . '</div>
                        </div>
                    ';
                }
            }
        }
        $result .= '</div>';
        return $result;
    }

    /**
     * {{ renderAddon('BootstrapCarousel||["1.jpg","2.jpg","3.jpg"]')|raw}}
     * @param array $vars
     * @return string
     */
    public function render($vars = []): string
    {
        $images = $vars;
        if(isset($vars['env'])){
            $images = \core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']];
        }

        $time = 5 * 1000;
        $rnd = time() . \core\utils\XWRandom::get(100);
        $html = '
            <div id="bs-carousel-generic_' . $rnd . '" class="carousel slide" data-ride="carousel" data-interval="' . $time . '">
              <!-- Indicators -->
              ' . $this->renderIndicators($images, $rnd) . '
            
              <!-- Wrapper for slides -->
              ' . $this->renderSlides($images) . '
            
              <!-- Controls -->
              <a class="left carousel-control" href="#bs-carousel-generic_' . $rnd . '" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#bs-carousel-generic_' . $rnd . '" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
        ';

        return $html;
    }
}