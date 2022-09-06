<?php

declare(strict_types=1);

namespace App\Controller\Admin\Post;

use App\Model\Post\Entity\PostRepository;
use App\Model\Post\UseCase\Activate;
use App\Model\Post\Entity\PostTranslationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post", name="admin.posts")
 * @return Response
 */
class PostController extends AbstractController
{
    private $posts;
    private $postTranslations;

    public function __construct(
        PostRepository $posts,
        PostTranslationRepository $postTranslations
    )
    {
        $this->posts = $posts;
        $this->postTranslations = $postTranslations;
    }

    /**
     * @Route("/{id}", name=".activate")
     * @param string $id
     * @param Activate\Handler $handler
     * @return Response
     */
    public function activate(string $id, Activate\Handler $handler): Response
    {
        $command = new Activate\Command($id);

        $handler->handle($command);

        $this->addFlash('success', 'Post activated.');
        return $this->redirectToRoute('home');
    }
}