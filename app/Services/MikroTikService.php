<?php
namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;

class MikroTikService
{
    protected Client $client;

    public function __construct(string $host, string $user, string $pass, int $port = 8728, int $timeout = 3)
    {
        // library will throw if it cannot connect
        $this->client = new Client([
            'host'    => $host,
            'user'    => $user,
            'pass'    => $pass,
            'port'    => $port,
            'timeout' => $timeout,
        ]);
    }

    /** simple identity check */
    public function getIdentity(): ?string
    {
        $query = new Query('/system/identity/print');
        $res = $this->client->query($query)->read();
        return $res[0]['name'] ?? null;
    }

    /** check if PPP/active has the username */
    public function isPppUserOnline(string $username): bool
    {
        $query = (new Query('/ppp/active/print'))->where('name', $username);
        $res = $this->client->query($query)->read();
        return !empty($res);
    }
}
