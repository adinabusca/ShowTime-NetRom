<?php

namespace App\Controller;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $entityManager->getRepository(Purchase::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')->addSelect('u')
            ->leftJoin('p.festival', 'f')->addSelect('f')
            ->getQuery();

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT results */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('purchase/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'UserController',
        ]);
    }
}
