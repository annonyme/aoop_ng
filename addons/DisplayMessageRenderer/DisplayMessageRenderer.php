<?php

class DisplayMessageRenderer extends \core\addons\XWAddonImplementation{
    public function render($vars = []): string{
        $messages = \core\utils\displayMessages\DisplayMessageFactory::instance()->getAllAndClear();
        return $this->renderTemplate('messages.twig', ['messages' => $messages, 'vars' => $vars]);
    }
}