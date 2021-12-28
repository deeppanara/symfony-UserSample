<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserAddress;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository, Security $security): Response
    {

        if ($security->isGranted('ROLE_ADMIN')) {
            $query = $userRepository->createQueryBuilder('u');
            if ($request->get('userName') ) {
                $query->andwhere('u.username LIKE :username')->setParameter('username', '%'.$request->get('userName').'%');
            }
            if ($request->get('email') ) {
                $query->andWhere('u.email LIKE :email')->setParameter('email', '%' . $request->get('email') . '%');
            }

            $data = $query->getQuery()->getResult();
        } else {
            $data[] = $security->getUser();
        }

        return $this->render('user/index.html.twig', [
            'users' => $data
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Security $security): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword($encoder->encodePassword($user, $form->get('password')->getData()));
            $entityManager->persist($user);
            $userProfile = $user->getUserProfile()->setUserId($user);
            $entityManager->persist($userProfile);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user, Security $security): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')  && $security->getUser()->getId() != $user->getId()) {
            return $this->redirectToRoute('user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Security $security): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')  && $security->getUser()->getId() != $user->getId()) {
            return $this->redirectToRoute('user_edit', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')  && $security->getUser()->getId() != $user->getId()) {
            return $this->redirectToRoute('user_edit', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
