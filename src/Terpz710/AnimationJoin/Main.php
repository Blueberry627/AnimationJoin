<?php

namespace Terpz710\JoinAnimation;

use pocketmine\plugin\PluginBase;
use pocketminr\utils\Config;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveDefaultConfig();
    }
}
