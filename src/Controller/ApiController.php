<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post_index", methods = {"GET"})
     * @param PostRepository $postRepository
     * @param SerializerInterface $serialize
     * @return Response
     */
    public function index(PostRepository $postRepository, SerializerInterface $serialize)
    {
        $posts = $postRepository->findAll();
        return $this->json($posts, 200, []); //on peut ajouter en quatrième paramètre le filtre groupe
    }
}
