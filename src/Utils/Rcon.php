<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Exceptions\ServerNotSelected;
use Microwin7\PHPUtils\Exceptions\RconConnectException;
use Microwin7\PHPUtils\Exceptions\SolutionDisabledException;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

class Rcon
{
    /** @var key-of<MainConfig::SERVERS>|string $server */
    protected string $server = '';

    /**
     * @param key-of<MainConfig::SERVERS>|string $server_name
     * @return $this
     */
    public function selectServer(string $server_name): static
    {
        $this->server = $server_name;
        return $this;
    }
    /**
     * @param string $command
     * @param string $username
     * @param boolean $check_correct_server
     * @return void
     * 
     * @throws RconConnectException
     */
    public function sendRconCommand(string $command, $username = '', $check_correct_server = true): void
    {
        $this->checkEmptyServer();
        if ($check_correct_server) $this->checkServer();
        $rcon = new \Thedudeguy\Rcon(
            MainConfig::SERVERS[$this->server]['host'],
            MainConfig::SERVERS[$this->server]['rcon']['port'],
            MainConfig::SERVERS[$this->server]['rcon']['password'],
            MainConfig::SERVERS[$this->server]['rcon']['timeout'],
        );
        if (!$rcon->connect()) throw new RconConnectException;
        $rcon->sendCommand($command . ' ' . $username);
        $rcon->disconnect();
    }
    /**
     * @return void
     * 
     * @throws ServerNotSelected
     */
    private function checkEmptyServer(): void
    {
        if (empty($this->server)) throw new ServerNotSelected;
    }
    /**
     * @return void
     * 
     * @throws SolutionDisabledException
     */
    private function checkServer(): void
    {
        if (!@MainConfig::SERVERS[Main::getCorrectServer($this->server)]['rcon']['enable']) throw new SolutionDisabledException;
    }
    /**
     * @param string $username
     * @return void
     * 
     * @throws RequiredArgumentMissingException
     */
    public function teleportToSpawn(string $username): void
    {
        if (empty($username)) throw new RequiredArgumentMissingException;
        $this->sendRconCommand('otp', $username);
    }
    /**
     * @param string $command
     * @return array
     */
    public function broadcast(string $command): array
    {
        $deny_servers = [];
        foreach (MainConfig::SERVERS as $server => $value) {
            if (!@$value['rcon']['enable']) continue;
            $this->server = $server;
            try {
                $this->sendRconCommand($command, '', false);
            } catch (RconConnectException) {
                $deny_servers[] = $server;
            } catch (SolutionDisabledException) {
            }
        }
        return $deny_servers;
    }
}
