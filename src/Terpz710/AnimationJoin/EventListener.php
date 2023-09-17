<?php

namespace Terpz710\JoinAnimation;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\plugin\Plugin;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        $chosenAnimation = $this->plugin->getConfig()->get("join-animation", "totem");

        if ($chosenAnimation === "totem") {
            $player->broadcastEntityEvent(ActorEventPacket::CONSUME_TOTEM);
        } elseif ($chosenAnimation === "guardian") {
            $player->getNetworkSession()->sendDataPacket(
                LevelEventPacket::create(
                    eventId: LevelEvent::GUARDIAN_CURSE,
                    eventData: 0,
                    position: $player->getPosition()
                )
            );
        }
    }
}
