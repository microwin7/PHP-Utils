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

    /**
     * Undocumented function
     *
     * @param string $server_name
     * @return $this
     */
    public function selectServer(string $server_name): static
    {
        $this->server = $server_name;
        return $this;
    }
    /**
     * Undocumented function
     *
     * @param string $command
     * @param string $username
     * @param boolean $check_correct_server
     * @return void
     * 
     * @throws \Microwin7\PHPUtils\Exceptions\RconConnectException
     */
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
    /**
     * Undocumented function
     *
     * @return void
     * 
     * @throws \Microwin7\PHPUtils\Exceptions\ServerNotSelected
     */
    private function checkEmptyServer(): void
    {
        if (empty($this->server)) throw new ServerNotSelected;
    }
    /**
     * Undocumented function
     *
     * @return void
     * 
     * @throws \Microwin7\PHPUtils\Exceptions\SolutionDisabledException
     */
    private function checkServer(): void
    {
        if (!@MainConfig::SERVERS[Main::getCorrectServer($this->server)]['rcon']['enable']) throw new SolutionDisabledException;
    }
    /**
     * Undocumented function
     *
     * @param string $username
     * @return void
     * 
     * @throws \Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException
     */
    public function teleportToSpawn(string $username): void
    {
        if (empty($username)) throw new RequiredArgumentMissingException;
        $this->sendRconCommand('otp', $username);
    }
    /**
     * Undocumented function
     *
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
            } catch (RconConnectException $e) {
                $deny_servers[] = $server;
            } catch (SolutionDisabledException $e) {
            }
        }
        return $deny_servers;
    }
}
