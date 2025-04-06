<?php
declare(strict_types=1);

namespace App\Services\Web3;

use Web3\Contract;
use Web3\Eth;
use Web3\Web3;

class ApiClient
{
    private const CONTRACT_ADDRESS = '0x76D00BEd14D0e30C7560E23EACEd161f07402A9f';
    private const HOST = 'http://localhost:8545';

    public function __construct(
        private readonly AbiProvider $abiProvider
    ) {
    }

    public function getData(string $functionName, array $params = [], callable $callback = null): void
    {
        $contract = new Contract(self::HOST, $this->abiProvider->getAbi());

        if ($params) {
            $contract->at(self::CONTRACT_ADDRESS)->call($functionName, $params);
        } else {
            $contract->at(self::CONTRACT_ADDRESS)->call($functionName, $params, $callback);
        }
    }
}
