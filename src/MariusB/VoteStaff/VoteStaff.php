<?php

namespace MariusB\VoteStaff;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;

class VoteStaff extends PluginBase {
    
    public $path;
    public $config;
    public $staff_voted;
    public $svoted = false;
    
    public function onEnable() {
        $this->getLogger()->info(TextFormat::DARK_GREEN . "VoteStaff Enabled");
        
        $this->path = $this->getDataFolder();
        @mkdir($this->path);
        if(!file_exists($this->path . "config.yml")) {
            $this->config = new Config($this->path . "config.yml", Config::YAML, array(
                "staff-count" => 1,
            ));
        } else {
            $this->getConfig()->save();
        }
    }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if($cmd->getName() === "v") {
            if($sender->hasPermission("votestaff.command")) {
                if($sender instanceof Player) {
                    if(!isset($args[0]) || count($args) < 1) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /v staff <player>");
                        return true;
                    }
                    
                    switch(strtolower($args[0])) {
                        case "staff":
                            if($sender->hasPermission("votestaff.command.staff")) {
                                if($sender instanceof Player) {
                                    if(isset($args[1])) {
                                        if($this->svoted == true) {
                                            $sender->sendMessage(TextFormat::GREEN . "You have already voted.");
                                        } else {
                                            $staff_count = $this->getConfig()->get("staff-count");
                                            $this->staff_voted = $this->staff_voted + 1;
                                            $name = $args[1];
                                            $sender->sendMessage(TextFormat::GREEN . "Your staff vote has been counted.");
                                            $sender->sendMessage(TextFormat::GREEN . "Voted: " . TextFormat::RED . $this->staff_voted . TextFormat::GREEN . TextFormat::GRAY . " |" . TextFormat::GREEN . " Needed: " . TextFormat::RED . $staff_count);
                                            $this->svoted = $this->svoted = true;
                                            if($this->staff_voted >= $staff_count) {
                                                $player = $this->getServer()->getPlayer($name);
                                                if($player->isOnline()) {
                                                    $command = "setgroup $name Helper";
                                                    $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $command);
                                                    $this->staff_voted = 0;
                                                    $this->svoted = false;
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "Player not found.");
                                                }
                                            }
                                        }
                                    } else {
                                        $sender->sendMessage(TextFormat::RED . "Usage: /v staff <player>");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED . "Permission: 'votestaff.command.staff' is missing.");
                            }
                            break;
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Permission: 'votestaff.command' is missing.");
            }
        }
    }
    
    public function onDisable() {
        $this->getConfig()->save();
        $this->getLogger()->info(TextFormat::DARK_RED . "VoteStaff Disabled");
    }
}
