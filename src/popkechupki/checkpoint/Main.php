<?php

declare(strict_types=1);

namespace popkechupki\checkpoint;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\math\Vector3;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class Main extends PluginBase {
    private Block $checkPointBlock;
    private bool $isAllowAddCheckPointToAll = false;
    private bool $isAllowDeleteCheckPointToAll = true;

    private array $playerCheckPoint = [];

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof ConsoleCommandSender) {
            $this->getLogger()->info("このコマンドはゲーム内からのみ実行可能です。");
            return true;
        }

        switch ($command->getName()) {
            case "cp":
                $this->teleportPlayer($sender);
                return true;
            case "addcp":
                if ($sender->hasPermission(DefaultPermissionNames::GROUP_OPERATOR) or $this->isAllowAddCheckPointToAll()) $this->setCheckPoint($sender, $this->getServer()->getPlayerByPrefix($sender->getName())->getPosition());
                return true;
            case "delcp":
                if ($sender->hasPermission(DefaultPermissionNames::GROUP_OPERATOR) or $this->isAllowDeleteCheckPointToAll()) {
                    $this->deleteCheckPoint($sender);
                    $sender->sendMessage(TextFormat::YELLOW."[CheckPoint] チェックポイントを削除しました。");
                }
                return true;
        }
        return false;
    }


    public function onEnable(): void {
        $this->initConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    private function initConfig(): void {
        if(!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0744, true);
        $config = new Config(
            $this->getDataFolder()."config.yml",
            Config::YAML,
            array(
                "CheckPointBlock" => BlockLegacyIds::EMERALD_BLOCK,
                "AllowAddCheckPointToAll" => false,
                "AllowDeleteCheckPointToAll" => true
            )
        );

        $this->checkPointBlock = BlockFactory::getInstance()->get($config->get("CheckPointBlock"), 0);
        $this->isAllowAddCheckPointToAll = $config->get("AllowAddCheckPointToAll");
        $this->isAllowDeleteCheckPointToAll = $config->get("AllowDeleteCheckPointToAll");
    }

    public function setCheckPoint(Player $player, ?Position $position): void {
        if ($this->hasCheckPoint($player)) $this->deleteCheckPoint($player);
        if ($position == null) {
            $this->playerCheckPoint = array_merge($this->playerCheckPoint, array($player->getName() => [
                "x" => $player->getPosition()->getFloorX(),
                "y" => $player->getPosition()->getFloorY(),
                "z" => $player->getPosition()->getFloorZ()
            ]));
        } else {
            $this->playerCheckPoint = array_merge($this->playerCheckPoint, array($player->getName() => [
                "x" => $position->getFloorX(),
                "y" => $position->getFloorY(),
                "z" => $position->getFloorZ()
            ]));
        }
        $player->sendMessage(TextFormat::AQUA."[CheckPoint] チェックポイントを作成しました。");
    }

    public function getCheckPoint(Player $player): ?Vector3 {
        if ($this->hasCheckPoint($player)) {
            return new Vector3(
                $this->playerCheckPoint[$player->getName()]["x"] + 0.5,
                $this->playerCheckPoint[$player->getName()]["y"] + 0.5,
                $this->playerCheckPoint[$player->getName()]["z"] + 0.5
            );
        } else {
            return null;
        }
    }

    public function deleteCheckPoint(Player $player): void {
        if ($this->hasCheckPoint($player)) unset($this->playerCheckPoint[$player->getName()]);
    }

    public function hasCheckPoint(Player $player): bool {
        return array_key_exists($player->getName(), $this->playerCheckPoint);
    }

    public function teleportPlayer(Player $player): void {
        if ($this->hasCheckPoint($player)) $player->teleport($this->getCheckPoint($player));
    }

    /**
     * @return bool
     */
    public function isAllowAddCheckPointToAll(): bool
    {
        return $this->isAllowAddCheckPointToAll;
    }

    /**
     * @return bool
     */
    public function isAllowDeleteCheckPointToAll(): bool
    {
        return $this->isAllowDeleteCheckPointToAll;
    }

    /**
     * @return Block
     */
    public function getCheckPointBlock(): Block
    {
        return $this->checkPointBlock;
    }
}

