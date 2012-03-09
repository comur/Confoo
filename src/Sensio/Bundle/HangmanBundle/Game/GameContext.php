<?php

namespace Sensio\Bundle\HangmanBundle\Game;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\HangmanBundle\Entity\GameData;

class GameContext
{
    private $em;

    private $user;

    private $repository;

    private $wordList;

    /**
     * @var GameData
     */
    private $data;

    public function __construct(ObjectManager $em, SecurityContext $securityContext, WordList $list)
    {
        $this->em = $em;
        $this->user = $securityContext->getToken()->getUser();
        $this->wordList = $list;

        $this->repository = $em->getRepository('SensioHangmanBundle:GameData');
        $this->repository->setUser($this->user);
    }

    public function getWordList()
    {
        return $this->wordList;
    }

    public function getGameData()
    {
        return $this->data;
    }

    public function newGame($length)
    {
        return new Game($this->getRandomWord($length));
    }

    public function getRandomWord($length)
    {
        return $this->wordList->getRandomWord($length);
    }

    public function loadGame($token)
    {
        try {
            $this->data = $this->repository->findGame($token);
        } catch (NoResultException $e) {
            return false;
        }

        return $this->data->toGame();
    }

    public function save(Game $game)
    {
        if (null === $this->data) {
            $this->data = new GameData();
            $this->data->setPlayer($this->user);
            $this->data->setWord($game->getWord());
        }

        $this->data->fromGame($game);
        $this->em->persist($this->data);
        $this->em->flush();
    }
}