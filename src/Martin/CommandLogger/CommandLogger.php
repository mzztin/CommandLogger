<?php

declare(strict_types=1);

namespace Martin\CommandLogger;

use Martin\CommandLogger\task\PostAsyncTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class CommandLogger extends PluginBase implements Listener
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $url;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if (!file_exists($this->getDataFolder()."config.yml")) {
            $this->saveResource("config.yml");
        }

        $config = new Config($this->getDataFolder() . "config.yml");
        $this->message = $config->get("message");
        $this->url = $config->get("webhook-url");
    }

    public function onCommandEvent(CommandEvent $event): void {
        $message = str_replace("{SENDER}", $event->getSender()->getName(), $this->message);
        $message = str_replace("{COMMAND}", $event->getCommand(), $message);
        $message = str_replace("@", "", $message); // Simply that u cant ping people!
        $this->getServer()->getAsyncPool()->submitTask(new PostAsyncTask($message, $this->url));
    }
}
