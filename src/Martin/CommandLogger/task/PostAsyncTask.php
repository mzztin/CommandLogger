<?php
namespace Martin\CommandLogger\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class PostAsyncTask extends AsyncTask
{
    /**
     * @var string
     */
    private $webhook;

    /**
     * @var string
     */
    private $message;

    public function __construct(string $message, string $webhook)
    {
        $this->webhook = $webhook;
        $this->message = $message;
    }

    public function onRun()
    {
        $curl = curl_init($this->getWebhook());
        curl_setopt_array($curl, array(
            CURLOPT_POSTFIELDS => json_encode(array(
                "content" => $this->getMessage()
            ), true),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"]
        ));
        $this->setResult(curl_exec($curl));
    }

    public function onCompletion(Server $server)
    {
        $response = $this->getResult();
        if($response !== ""){
            $server->getLogger()->error("[CommandLogger] Got error: " . $response);
        }
    }

    /**
     * @return string
     */
    public function getWebhook(): string
    {
        return $this->webhook;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}