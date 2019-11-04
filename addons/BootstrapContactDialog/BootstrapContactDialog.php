<?php
class BootstrapContactDialog extends \core\addons\XWAddonImplementation{
    /**
     * {{ renderAddon('BootstrapContactDialog||["link":"","title":"","text":""]')|raw}}
     * {{ renderAddon('BootstrapContactDialog||["env":"contactData"]')|raw}} read the contactData-var from env-data
     * @param array $vars
     * @return string
     */
    public function render($vars = []): string{
        $imageLink = isset($vars['link']) ? $vars['link'] : '';
        $title = isset($vars['title']) ? $vars['title'] : '';
        $text = isset($vars['text']) ? $vars['text'] : '';
        if(isset($vars['env'])){
            if(isset(\core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['link'])){
                $imageLink = \core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['link'];
            }
            if(isset(\core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['title'])){
                $title = \core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['title'];
            }
            if(isset(\core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['text'])){
                $text = \core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['text'];
            }

            if(isset(\core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['page'])){
                try{
                    $paths = [\core\utils\XWServerInstanceToolKit::instance()->getCurrentInstanceDeploymentRootPath() . 'pages'];
                    $loader = new \Twig_Loader_Filesystem($paths);
                    $twig = new \Twig_Environment($loader);
                    $twig = \core\twig\TwigFunctions::decorateTwig($twig);

                    $text = $twig->render(\core\utils\XWServerInstanceToolKit::instance()->getEnvValues()[$vars['env']]['page'], []);
                }
                catch(Exception $e){

                }
            }
        }
        $html = '';

        if(strlen(trim($imageLink)) > 0){
            $html = "                
                <style type=\"text/css\">                                      
                    .bcd-trigger{
                        position:fixed;

                        cursor:pointer;
                        border-top:4px solid white;
                        border-left:4px solid white;
                        border-right:4px solid white;
                        border-bottom:4px solid white;
                        background-size:cover;
                        background-image: url(" . $imageLink . ");
                        border-radius: 5px 0 0 5px;
                        z-index: 100;
                        
                        animation-duration: .2s;
                        animation-name: bcd-ani;
                        
                        outline: 1px solid #888888;
                        -moz-outline-radius: 5px 0 0 5px;
                    }
                    
                    @keyframes bcd-ani {
                        0% {
                            transform: scale(1);
                            opacity: 1;
                        }
                        70% {
                            transform: scale(.85);
                            opacity: 0.85;
                        }
                        100% {
                            transform: scale(1);
                            opacity: 1;
                        }
                    }
                </style>
                <!-- trigger -->
                <div class=\"bcd-trigger\" data-toggle=\"modal\" data-target=\"#addonContactModal\"
                ></div>
                <!-- Modal -->
                <div class=\"modal fade\" id=\"addonContactModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\">
                  <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                      <div class=\"modal-header\">
                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                        <h4 class=\"modal-title\" id=\"myModalLabel\">" . $title . "</h4>
                      </div>
                      <div class=\"modal-body\">
                        " . $text .  "
                      </div>
                      <div class=\"modal-footer\">
                        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
            ";
        }

        return $html;
    }
}