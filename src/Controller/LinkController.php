<?php

namespace App\Controller;

use App\Service\LinkService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

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
        $data = $this->getSerializer()->normalize($link, 'json', ['groups' => 'public']);
        return $this->json($data);
    }

    /**
     * @Route("/link", name="get_all_links", methods={"GET"})
     */
    public function getAllLinks()
    {
        $user = $this->getUser();
        $links = $this->linkService->getAllLinks($user);
        $data = $this->getSerializer()->normalize($links, 'json', ['groups' => 'public']);
        return $this->json($data);
    }

    /**
     * @Route("/go/{pretty}", name="go_link", methods={"GET"})
     */
    public function goLink(string $pretty)
    {
        $user = $this->getUser();
        $url = $this->linkService->getOriginalUrl($pretty, $user);
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
        $user = $this->getUser();
        $data = $this->getSerializer()->decode($request->getContent(), 'json');
        $original = $data['url'];
        $link = $this->linkService->addLink($original, $user);
        $data = $this->getSerializer()->normalize($link, 'json', ['groups' => 'public']);
        return $this->json($data);
    }

    /**
     * @Route("/link/{id}", name="update_link", methods={"POST"})
     */
    public function updateLink(int $id, Request $request)
    {
        $link = $this->findLink($id);
        $data = $this->getSerializer()->decode($request->getContent(), 'json');
        $original = $data['url'];
        $link = $this->linkService->updateLink($link, $original);
        $data = $this->getSerializer()->normalize($link, 'json', ['groups' => 'public']);
        return $this->json($data);
    }

    /**
     * @Route("/link/{id}", name="remove_link", methods={"DELETE"})
     */
    public function removeLink(int $id)
    {
        $user = $this->getUser();
        $link = $this->findLink($id);
        $this->linkService->removeLink($link);
        return $this->json(null, 204);
    }

    private function findLink($id)
    {
        $user = $this->getUser();
        $link = $this->linkService->getLink($id, $user);
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
