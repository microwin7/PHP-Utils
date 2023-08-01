<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Exceptions\RconConnectException;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;
use Microwin7\PHPUtils\Exceptions\SolutionDisabledException;
use Microwin7\PHPUtils\Exceptions\ServerNotSelected;

class Rcon
{
    protected $server;

    public function selectServer(string $server_name)
    {
        $this->server = $server_name;
        return $this;
    }
    public function sendRconCommand(string $command, $username = '', $check_correct_server = true): void
    {
        $this->checkEmptyServer();
        if ($check_correct_server) $this->checkServer();
        $rcon = new \Thedudeguy\Rcon(
            MainConfig::SERVERS[$this->server]['host'],
            MainConfig::SERVERS[$this->server]['port'],
            MainConfig::SERVERS[$this->server]['rcon']['password'],
            MainConfig::SERVERS[$this->server]['rcon']['timeout'],
        );
        if (!$rcon->connect()) throw new RconConnectException;
        $rcon->sendCommand($command . ' ' . $username);
        $rcon->disconnect();
    }
    private function checkEmptyServer(): void
    {
        if (empty($this->server)) throw new ServerNotSelected;
    }
    private function checkServer()
    {
        if (!@MainConfig::SERVERS[Main::getCorrectServer($this->server)]['rcon']['enable']) throw new SolutionDisabledException;
    }
    public function teleportToSpawn(string $username): void
    {
        if (empty($username)) throw new RequiredArgumentMissingException;
        $this->sendRconCommand('otp', $username);
    }
    public function broadcast(string $command): array
    {
        $deny_servers = [];
        foreach (MainConfig::SERVERS as $server => $value) {
            if (!@$value['rcon']['enable']) continue;
            $this->server = $server;
            try {
                $this->sendRconCommand($command, '', false);
            } catch (RconConnectException $e) {
                $deny_servers[] = $server;
            } catch (SolutionDisabledException $e) {
            }
        }
        return $deny_servers;
    }
}
