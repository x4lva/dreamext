<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Post\Entity\Post;
use App\Model\Post\Entity\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\ReadModel\Post\Filter;

class HomeController extends AbstractController
{
    private $posts;

    public function __construct(
        PostRepository $posts
    )
    {
        $this->posts = $posts;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $filter = new Filter\Filter();
        $filter->status = Post::STATUS_ACTIVE;

        if ($this->getUser() !== null && in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            $filter->status = '';
        }

        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $this->posts->all(
            $filter,
            $request->query->getInt('page', 1),
            10,
            $request->query->get('sort', 'p.date'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/home.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}