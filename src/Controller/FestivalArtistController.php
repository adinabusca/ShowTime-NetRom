<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Festival;
use App\Entity\FestivalArtist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

    #[Route('/festival/artist/new', name: 'app_festival_artist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $festivalArtist = new FestivalArtist();

        $form = $this->createFormBuilder($festivalArtist)
            ->add('festival', EntityType::class, [
                'class' => Festival::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a festival',
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'name',
                'placeholder' => 'Select an artist',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($festivalArtist);
            $entityManager->flush();

            return $this->redirectToRoute('app_festival_artist');
        }

        return $this->render('festival_artist/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
