<?php

namespace Terpz710\AnimationJoin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

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
            $this->sendTotemAnimation($player);
            $this->sendTotemSound($player);
        } elseif ($animationType === "guardian") {
            $player->getInventory()->setItemInHand(Item::get(Item::TOTEM));
            $this->sendGuardianAnimation($player);
            $this->sendGuardianSound($player);
        }
    }

    private function sendTotemAnimation(Player $player) {
        $pk = LevelEventPacket::create(
            eventId: LevelEvent::GUARDIAN_CURSE, // Corrected totem animation
            eventData: 0,
            position: $player->getPosition()
        );
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    private function sendTotemSound(Player $player) {
        $pk = new PlaySoundPacket();
        $pk->soundName = "item.totem.use";
        $pk->x = $player->getX();
        $pk->y = $player->getY();
        $pk->z = $player->getZ();
        $pk->volume = 1.0;
        $pk->pitch = 1.0;
        $player->sendDataPacket($pk);
    }

    private function sendGuardianAnimation(Player $player) {
        $pk = ActorEventPacket::create(
            entityId: $player->getId(),
            event: ActorEventPacket::CONSUME_TOTEM
        );
        $player->broadcastEntityEvent($pk);
    }

    private function sendGuardianSound(Player $player) {
        $pk = new PlaySoundPacket();
        $pk->soundName = "entity.guardian.curse";
        $pk->x = $player->getX();
        $pk->y = $player->getY();
        $pk->z = $player->getZ();
        $pk->volume = 1.0;
        $pk->pitch = 1.0;
        $player->sendDataPacket($pk);
    }
}
