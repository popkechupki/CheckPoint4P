<?php

declare(strict_types=1);

namespace popkechupki\checkpoint;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener {
    public Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        if ($event->getBlock()->getName() !== $this->plugin->getCheckPointBlock()->getName()) return;
        $this->plugin->setCheckPoint($event->getPlayer(), $event->getBlock()->getPosition());
    }
}