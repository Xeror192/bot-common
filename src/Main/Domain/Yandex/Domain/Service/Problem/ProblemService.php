<?php

namespace Jefero\Bot\Main\Domain\Yandex\Domain\Service\Problem;

use Jefero\Bot\Main\Application\Yandex\CallbackHandler;
use Jefero\Bot\Main\Domain\Yandex\Domain\Entity\Problem;
use Jefero\Bot\Main\Domain\Yandex\Domain\Service\ProblemRepository;

class ProblemService
{
    private ProblemRepository $repository;

    public function __construct(ProblemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findProblem(string $query): ?Problem
    {
        $problems = $this->repository->findAll();
        $mathProblem = null;
        $mathProblemScore = 0;
        foreach ($problems as $problem) {
            if ($this->match($query, $problem) > $mathProblemScore) {
                $mathProblem = $problem;
                $mathProblemScore = $this->match($query, $problem);
            }
        }

        return $mathProblem;
    }

    public function match(string $query, Problem $problem): int
    {
        $response = 0;

        foreach ($problem->getKeywords() as $keyword) {
            if (str_contains($query, $keyword)) {
                $response++;
            }
        }

        return $response;
    }

    public function getShockScore(): int
    {
        $user = CallbackHandler::$currentUser;

        if(empty($user->emotions)) {
            return 0;
        }
        /** @var Problem $problem */
        $problem = null;
        if($user->problemCode) {
            $problem = $this->repository->findByCode($user->problemCode);
        }

        $emotionScore = 0;

        foreach ($user->emotions as $emotion) {
            $emotionScore += $emotion->score * $emotion->factor;
        }
        $emotionScore = round($emotionScore / count($user->emotions));

        return $problem ? $emotionScore * $problem->getScore() : $emotionScore * 5;
    }

    public function getProblemDescription(string $problemCode): string
    {
        switch ($problemCode) {
            case 'relationship': {
                return 'Трудности в понимании себя, своего партнера. Сначала весь ты, потом весь мир';
            }
            case 'search_yourself': {
                return 'Говоря о себе, мы без особого труда перечисляем те черты, которые привыкли считать своими. Но это не всегда так';
            }
        }

        return '';
    }
}