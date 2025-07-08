<?php

namespace App\Controller;
use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

final class ArtistController extends AbstractController
{
    #[Route('/artist', name: 'app_artist', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $artistSearch = $request->query->get('artist');

        $queryBuilder = $entityManager->getRepository(Artist::class)
            ->createQueryBuilder('a');

        if ($artistSearch) {
            $queryBuilder->andWhere('LOWER(a.name) LIKE :artistSearch')
                ->setParameter('artistSearch', '%'. strtolower($artistSearch).'%');
        }

        $queryBuilder->orderBy('a.name', 'ASC');

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT results */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('artist/index.html.twig', [
                'pagination' => $pagination,
                'artistSearch' => $artistSearch,
            ]);
        }

        return $this->render('artist/customer_index.html.twig', [
            'pagination' => $pagination,
            'artistSearch' => $artistSearch,
        ]);
    }
    #[Route('/artist/show/{id}', name: 'app_artist_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $artist = $entityManager->getRepository(Artist::class)->find($id);

        if (!$artist) {
            throw $this->createNotFoundException();
        }
        return $this->render('artist/show.html.twig', ['artist' => $artist]);
    }

    #[Route('/artist/new', name: 'app_artist_new', methods: ['GET','POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {

        $artist = new Artist();

        $form = $this ->createFormBuilder($artist)
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                    new Assert\Regex([
                        'pattern' => '/^[\w\s.,!?\'-]+$/u',
                        'message' => 'Only letters, numbers, spaces and punctuation are allowed.',
                    ]),
                ],
            ])
            ->add('music_genre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $artist = $form->getData();
            $entityManager->persist($artist);
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_show', ['id' =>(int)$artist->getId()]);
        }

        return $this->render('artist/new.html.twig', [
            'form' => $form,
        ]);



    }

    #[Route('/artist/edit/{id}', name: 'app_artist_edit', methods: ['GET','POST'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $artist = $entityManager->getRepository(Artist::class)->find($id);
        if (!$artist) {
            throw $this->createNotFoundException();
        }

        $form = $this ->createFormBuilder($artist)
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                    new Assert\Regex([
                        'pattern' => '/^[\w\s.,!?\'-]+$/u',
                        'message' => 'Only letters, numbers, spaces and punctuation are allowed.',
                    ]),
                ],
            ])
            ->add('music_genre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $artist = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_show', ['id' =>(int)$artist->getId()]);

        }

        return $this->render('artist/edit.html.twig', ['form' => $form,]);
    }
}
