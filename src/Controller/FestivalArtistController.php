<?php

namespace App\Controller;

use App\Entity\FestivalArtist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FestivalArtistController extends AbstractController
{
    #[Route('/festival/artist', name: 'app_festival_artist',  methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {

        $queryBuilder = $entityManager->getRepository(FestivalArtist::class)
            ->createQueryBuilder('fa')
            ->leftJoin('fa.festival', 'f')->addSelect('f')
            ->leftJoin('fa.artist', 'a')->addSelect('a')
            ->getQuery();

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT results */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('festival_artist/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('festival/artist/delete/{id}', name: 'app_festival_artist_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $performance = $entityManager->getRepository(FestivalArtist::class)->find($id);

        if (!$performance) {
            throw $this->createNotFoundException('No performance found for id ' . $id);
        }

        $entityManager->remove($performance);
        $entityManager->flush();



        return $this->redirectToRoute('app_festival_artist');
    }
}
