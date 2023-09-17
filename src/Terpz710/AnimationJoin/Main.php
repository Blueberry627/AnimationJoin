<?php

namespace Terpz710\AnimationJoin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    /** @var Config */
    private $config;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $animationType = $this->config->get("animation_type", "totem");

        if ($animationType === "totem") {
            $this->sendCustomAnimation($player, ActorEvent::CONSUME_TOTEM);
        } elseif ($animationType === "guardian") {
            $player->addEffect(Effect::getEffect(Effect::GUARDIAN_CURSE)->setDuration(200));
        }
    }

    private function sendCustomAnimation(Player $player, int $eventId) {
        $pk = new ActorEventPacket();
        $pk->entityRuntimeId = $player->getId();
        $pk->event = $eventId;
        $player->broadcastEntityEvent($pk);
    }
}
