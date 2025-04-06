<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Web3\Personal;
use Web3\Web3;
use Web3\Contract;
use App\Models\Question;
use App\Models\Vote;

class VotingController extends Controller
{
    protected Web3 $web3;
    protected Contract $contract;

    public function __construct()
    {
        $this->web3 = new Web3(config('web3.rpc'), 30);
        $abi = json_decode(file_get_contents(storage_path('abi/Voting.json')), true);
        $this->contract = new Contract($this->web3->provider, $abi);
        $this->contract->at(config('web3.contract_address'));

        $user = auth()->user(); // Получаем текущего пользователя
        $account = $user->eth_account ?? ''; // Получаем Ethereum аккаунт из базы
        $password = $user->eth_password ?? ''; // Получаем Ethereum пароль из базы

        $personal = new Personal($this->web3->provider);
        $personal->unlockAccount($account, $password, 3600, function ($err, $result) {
        });
    }

    // --- Local DB endpoints ---

    public function list()
    {
        $user = User::query()->find(auth()->id());

        // Проверяем, есть ли департаменты у пользователя
        if (!$user || $user->departments->isEmpty()) {
            return response()->json(['message' => 'No departments found for this user'], 404);
        }

        // Собираем все вопросы (голосования) из всех департаментов
        $votes = $user->departments->flatMap(function ($department) {
            $res = [];
            foreach ($department->questions as $question) {
                $voterTotal = $department->users()->count();
                $res[] = [
                    'id' => (string) $question->id,
                    'title' => (string) $question->title,
                    'description' => (string) $question->description,
                    'votersCount' => rand(0, $voterTotal),
                    'votersTotal' => $voterTotal,
                    'endDate' => $question->end_date,
                    'departmentId' => (string) $question->department_id,
                ];
            }

            return $res;
        });

        return response()->json($votes);
    }

    public function detail($id)
    {
        $question = Question::with(['files', 'protocolFiles'])->find($id);
        $files = [];
        foreach ($question->files as $file) {
            $files[] = [
                'id' => (string) $file->id,
                'name' => $file->name,
                'path' => $file->path,
            ];
        }

        if (!$question) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json([
            'id' => (string) $question->id,
            'title' => (string) $question->title,
            'description' => (string) $question->description,
            'departmentId' => (string) $question->department_id,
            'files' => $files,
            'votersCount' => rand(0, $question->department->users()->count()),
            'votersTotal' => $question->department->users()->count(),
            'votingType' => $question->voting_type,
            'votingWay' => $question->voting_way,
            'conferenceLink' => $question->conference_link
        ]);
    }

    public function vote(Request $request)
    {
        $data = $request->validate([
            'questionId' => 'required|exists:questions,id',
            'answerId' => 'required|in:0,1,2'
        ]);

        $canVote = Question::canUserVote(auth()->user(), $data['questionId']);

        if (!$canVote) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Vote::create([
            'user_id' => auth()->id(),
            'question_id' => $data['questionId'],
            'answer' => $data['answerId'],
        ]);

        return response()->json(['message' => 'Vote accepted']);
    }

    public function history()
    {
        $history = Question::with(['protocolFiles'])->where('is_closed', true)->get();
        return response()->json($history);
    }

    // --- Blockchain endpoints ---

    public function createVote(Request $request)
    {
        $desc = $request->input('description');
        $duration = $request->input('duration');
        $type = (int) $request->input('vote_type');
        $voters = $request->input('voters');

        $this->contract->send('createVote',
            $desc,
            $duration,
            $type,
            $voters,
            [
                'from' => config('web3.wallet_address'),
                'gas' => '500000',
            ],
            function ($err, $tx) {
                if ($err) return response()->json(['error' => $err->getMessage()], 500);
                return response()->json(['tx' => $tx]);
            }
        );
    }

    public function voteOnChain(Request $request, int $voteId)
    {
        $decision = (bool) $request->input('decision');

        $error = '';
        $trx = '';
        $this->contract->send('vote',
            $voteId,
            $decision,
            [
                'from' => config('web3.wallet_address')
            ],
            function ($err, $tx) use (&$error, &$trx) {
                if ($err) {
                    $error = $err->getMessage();
                };
                $trx = $tx;
            }
        );

        if ($error) {
            return response()->json(['error' => $error], 500);
        }

        return response()->json(['tx' => $trx]);
    }

    public function endVoting(int $voteId)
    {
        $this->contract->send('endVoting',
            $voteId,
            [
                'from' => config('web3.wallet_address'),
                'gas' => '300000',
            ],
            function ($err, $tx) {
                if ($err) return response()->json(['error' => $err->getMessage()], 500);
                return response()->json(['tx' => $tx]);
            }
        );
    }

    public function getAllVotes()
    {
        $this->contract->call('getAllVotes', function ($err, $votes) {
            if ($err) return response()->json(['error' => $err->getMessage()], 500);
            return response()->json(['votes' => $votes]);
        });
    }
}
