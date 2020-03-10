<?php

namespace App\Service;

use App\Repository\LinkRepository;
use App\Entity\Link;
use App\Entity\User;

class LinkService
{
    /**
     * @var LinkRepository
     */
    private $linkRepository;

    public function __construct(LinkRepository $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function getLink(int $id, User $user)
    {
        return $this->linkRepository->findOneByIdAndUser($id, $user);
    }

    public function getOriginalUrl(string $pretty, User $user): ?string
    {
        $link = $this->linkRepository->findOneByPrettyAndUser($pretty, $user);
        return $link ? $link->getOriginal() : '';
    }

    public function getAllLinks(User $user)
    {
        return $this->linkRepository->findByUser($user);
    }

    public function addLink(string $original, User $user): Link
    {
        $link = $this->linkRepository->findOneByOriginalAndUser($original, $user);
        if (!$link) {
            $pretty = $this->generatePrettyHash($original);
            $link = $this->linkRepository->create($original, $pretty, $user);
            return $this->linkRepository->save($link);
        }
        return $link;
    }

    public function updateLink(Link $link, string $original): Link
    {
        $link->setOriginal($original);
        return $this->linkRepository->save($link);
    }

    public function removeLink(Link $link)
    {
        $this->linkRepository->remove($link);
    }

    private function generatePrettyHash(string $original)
    {
        return hash('sha1', $original);
    }
}
