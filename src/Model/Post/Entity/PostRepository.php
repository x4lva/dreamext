<?php

declare(strict_types=1);

namespace App\Model\Post\Entity;

use App\ReadModel\Post\Filter\Filter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PostRepository
{
    private $em;
    private $repo;
    private $paginator;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->repo = $em->getRepository(Post::class);
    }

    public function get(string $id): Post
    {
        /** @var Post $post */
        if (!$post = $this->repo->find($id)) {
            throw new EntityNotFoundException('Post is not found.');
        }
        return $post;
    }

    public function getByUser(string $id): array
    {
        return $this->repo->findBy(['user' => $id]);
    }

    public function add(Post $post): void
    {
        $this->em->persist($post);
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->repo->createQueryBuilder('p')
            ->leftJoin('p.translations', 't')
            ->leftJoin('p.user', 'u');

        if ($filter->title) {
            $qb->andWhere('t.title LIKE :title')
                ->setParameter('title', '%' . mb_strtolower($filter->title) . '%');
        }

        if (!\in_array($sort, ['p.date', 'p.status', 'u.email'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}