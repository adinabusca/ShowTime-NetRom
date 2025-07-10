<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Userdetails;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[Route('/userdetails/new/{userId}', name: 'app_userdetails_new', methods: ['GET', 'POST'])]
    public function newDetails(int $userId, EntityManagerInterface $em, Request $request): Response
    {
        $user = $em->getRepository(User::class)->find($userId);



        $details = new Userdetails();
        $details->setUser($user);
        $details->setRole('CUSTOMER');

        $form = $this->createFormBuilder($details)
            ->add('firstName', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('age', IntegerType::class, [
                'constraints' => [new Assert\Positive()],
            ])
            ->add('save', SubmitType::class, [])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($details);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('userdetails/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

}
