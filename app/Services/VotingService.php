<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\Web3\ApiClient;

class VotingService
{
    public function __construct(
        private readonly ApiClient $apiClient,
    ) {

    }

    public function getVotingCount(): int
    {
        $voteCount = 0;
        $this->apiClient->getData('voteCount', [], function ($err, $result) use (&$voteCount) {
            $voteCount = (int) reset($result)->__toString();
        });

        return $voteCount;
    }

    public function getAllVoting(): array
    {
        $voting = [];
        $this->apiClient->getData('getAllVotes', [], function ($err, $result) use (&$voting) {
            $voting = (int) reset($result)->__toString();
        });

        return $voting;
    }
}
