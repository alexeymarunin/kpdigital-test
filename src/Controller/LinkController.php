<?php

namespace App\Controller;

use App\Service\LinkService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LinkController extends AbstractController
{
    /**
     * @var LinkService
     */
    private $linkService;

    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    /**
     * @Route("/link/{id}", name="get_link", methods={"GET"})
     */
    public function getLink(int $id)
    {
        $link = $this->findLink($id);
        return $this->json($link);
    }

    /**
     * @Route("/link", name="get_all_links", methods={"GET"})
     */
    public function getAllLinks()
    {
        $links = $this->linkService->getAllLinks();
        return $this->json($links);
    }

    /**
     * @Route("/go/{pretty}", name="go_link", methods={"GET"})
     */
    public function goLink(string $pretty)
    {
        $url = $this->linkService->getOriginalUrl($pretty);
        if (!$url) {
            throw $this->createNotFoundException('No link found');
        }
        return $this->redirect($url);
    }

    /**
     * @Route("/link", name="add_new_link", methods={"PUT"})
     */
    public function addNewLink(Request $request)
    {
        $data = $this->getSerializer()->decode($request->getContent(), 'json');
        $original = $data['url'];
        $link = $this->linkService->addLink($original);
        return $this->json($link);
    }

    /**
     * @Route("/link/{id}", name="update_link", methods={"POST"})
     */
    public function updateLink(int $id, Request $request)
    {
        $link = $this->findLink($id);
        $data = $this->getSerializer()->decode($request->getContent(), 'json');
        $original  = $data['url'];
        $link = $this->linkService->updateLink($link, $original);
        return $this->json($link);
    }

    /**
     * @Route("/link/{id}", name="remove_link", methods={"DELETE"})
     */
    public function removeLink(int $id)
    {
        $link = $this->findLink($id);
        $this->linkService->removeLink($link);
        return $this->json(null, 204);
    }

    private function findLink($id)
    {
        $link = $this->linkService->getLink($id);
        if (!$link) {
            throw $this->createNotFoundException('No link found');
        }
        return $link;
    }

    private function getSerializer()
    {
        return $this->container->get('serializer');
    }
}
