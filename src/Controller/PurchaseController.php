<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Purchase;
use App\Entity\User;
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
    #[Route('/purchases/user/{id}', name: 'app_user_purchase_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager,PaginatorInterface $paginator, Request $request, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }
        $queryBuilder = $entityManager->getRepository(Purchase::class)
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5 // Items per page
        );

        return $this->render('purchase/show.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/purchase/confirm/{festivalId}', name: 'app_purchase_confirm', methods: ['GET'])]
    public function confirm(int $festivalId, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        $festival = $entityManager->getRepository(Festival::class)->find($festivalId);
        if (!$festival) {
            throw $this->createNotFoundException('Festival not found');
        }

        return $this->render('purchase/confirm.html.twig', [
            'festival' => $festival,
            'user' => $user,
        ]);
    }

    #[Route('/purchases/new/{festivalId}', name: 'app_purchase_new', methods: ['GET', 'POST'])]
    public function new(int $festivalId,EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        $festival = $entityManager->getRepository(Festival::class)->find($festivalId);

        if (!$festival) {
            throw $this->createNotFoundException('Festival not found');
        }

        $ticketCount = (int) $request->request->get('ticket_count', 1);
        if ($ticketCount < 1 || $ticketCount > 10) {
            throw new \InvalidArgumentException('Invalid number of tickets.');
        }

        for ($i = 0; $i < $ticketCount; $i++){
            $purchase = new Purchase();
            $purchase->setUser($user);
            $purchase->setFestival($festival);

            $entityManager->persist($purchase);

        }

        $entityManager->flush();

        return $this->redirectToRoute('app_user_purchase_show', ['id' => $user->getId()]);


    }
}
