<?php

namespace App\Controller;

use App\Entity\Userdetails;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserdetailsController extends AbstractController
{
    #[Route('/userdetails', name: 'app_userdetails',  methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $entityManager->getRepository(Userdetails::class)
            ->createQueryBuilder('ud')
            ->leftJoin('ud.user', 'u')->addSelect('u')
            ->getQuery();

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT results */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('userdetails/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'UserController',
        ]);

    }
}
