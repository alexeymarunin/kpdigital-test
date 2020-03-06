<?php

namespace App\Service;

use App\Repository\LinkRepository;

use Psr\Container\ContainerInterface;

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

    public function getLink(int $id)
    {
        return $this->linkRepository->find($id);
    }

    public function getOriginalUrl(string $pretty)
    {
        $link = $this->linkRepository->findOneBy(['pretty' => $pretty]);
        return $link ? $link->getOriginal() : '';
    }

    public function getAllLinks()
    {
        return $this->linkRepository->findAll();
    }

    public function addLink(string $original)
    {
        $link = $this->linkRepository->findOneBy(['original' => $original]);
        if (!$link) {
            $pretty = $this->generatePrettyHash($original);
            $link = $this->linkRepository->saveLink($original, $pretty);
        }
        return $link;
    }

    public function updateLink($link, string $original)
    {
        $pretty = $this->generatePrettyHash($original);
        $link->setPretty($pretty);
        $link->setOriginal($original);
        return $this->linkRepository->updateLink($link);
    }

    public function removeLink($link)
    {
        $this->linkRepository->removeLink($link);
    }

    private function generatePrettyHash(string $original)
    {
        return hash('crc32', $original);
    }
}
