<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\ErrorHandler;
use App\Model\Post\Entity\PostRepository;
use App\Model\Post\Entity\PostTranslationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Post\UseCase\Create;
use App\Model\Post\UseCase\Edit;

/**
 * @Route("/posts", name="posts")
 */
class PostController extends AbstractController
{
    private $errors;
    private $posts;
    private $postTranslations;

    public function __construct(
        ErrorHandler $errors,
        PostRepository $posts,
        PostTranslationRepository $postTranslations
    )
    {
        $this->errors = $errors;
        $this->posts = $posts;
        $this->postTranslations = $postTranslations;
    }

    /**
     * @Route("/", name=".list")
     * @return Response
     */
    public function index(): Response
    {
        $posts = $this->posts->getByUser($this->getUser()->getId());

        return $this->render('app/post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name=".create")
     * @param Create\Handler $handler
     * @param Request $request
     * @return Response
     * @throws LogicException
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command($this->getUser()->getId());

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');

                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}", name=".view")
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function view(Request $request, string $slug): Response
    {
        $locale = $request->getLocale();
        $postTranslation = $this->postTranslations->getByLocaleAndSlug($locale, $slug);

        if (!$postTranslation) {
            die('no post found.');
        }

        if ($postTranslation->getSlug() !== $slug) {
            return $this->redirectToRoute(
                $request->get('_route'),
                [
                    '_locale' => $postTranslation->getLanguageCode(),
                    'slug' => $postTranslation->getSlug(),
                ]
            );
        }

        return $this->render('app/post/view.html.twig', [
            'postTranslation' => $postTranslation
        ]);
    }

    /**
     * @Route("/edit/{id}", name=".edit")
     * @param Request $request
     * @param Edit\Handler $handler
     * @param string $id
     * @return Response
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function edit(Request $request, Edit\Handler $handler, string $id): Response
    {
        $post = $this->posts->get($id);

        $command = Edit\Command::fromPost($post);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}