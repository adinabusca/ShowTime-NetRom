<?php

namespace App\Controller;

use App\Entity\Festival;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class FestivalController extends AbstractController
{
    #[Route('/festival', name: 'app_festival', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {

        $festivalSearch = $request->query->get('festival');

        $queryBuilder = $entityManager->getRepository(Festival::class)
            ->createQueryBuilder('f');

        if ($festivalSearch) {
            $queryBuilder->andWhere('LOWER(f.name) LIKE :festivalSearch')
                ->setParameter('festivalSearch', '%' . strtolower($festivalSearch) . '%');
        }

        $queryBuilder->orderBy('f.name', 'ASC');

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT results */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('festival/index.html.twig', [
                'pagination' => $pagination,
                'festivalSearch' => $festivalSearch,
            ]);
        }


        return $this->render('festival/customer_index.html.twig', [
            'pagination' => $pagination,
            'festivalSearch' => $festivalSearch,
        ]);
    }
    #[Route('/festival/show/{id}', name: 'app_festival_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $festival = $entityManager->getRepository(Festival::class)->find($id);

        if (!$festival) {
            throw $this->createNotFoundException();
        }
        return $this->render('festival/show.html.twig', ['festival' => $festival]);
    }

    #[Route('/festival/new', name: 'app_festival_new', methods: ['GET','POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {

        $festival = new Festival();

        $form = $this ->createFormBuilder($festival)
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
            ->add('location', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('startdate', DateType::class)
            ->add('enddate', DateType::class)
            ->add('price', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(0),
                ],
            ])
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $festival = $form->getData();
            $entityManager->persist($festival);
            $entityManager->flush();
            return $this->redirectToRoute('app_festival_show', ['id' =>(int)$festival->getId()]);
        }

        return $this->render('festival/new.html.twig', [
            'form' => $form,
        ]);



    }

    #[Route('/festival/edit/{id}', name: 'app_festival_edit', methods: ['GET','POST'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $festival = $entityManager->getRepository(Festival::class)->find($id);
        if (!$festival) {
            throw $this->createNotFoundException();
        }

        $form = $this ->createFormBuilder($festival)
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
            ->add('location', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('startdate', DateType::class,)
            ->add('enddate', DateType::class)
            ->add('price', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(0),
                ],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $festival = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('app_festival_show', ['id' =>(int)$festival->getId()]);

        }

        return $this->render('festival/edit.html.twig', ['form' => $form,]);
    }

}
