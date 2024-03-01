<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Form\EditType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository,PaginatorInterface $paginator,Request $request ): Response
    {  
        //$userRepository = $this->getDoctrine()->getRepository(User::class);
        $query = $userRepository->createQueryBuilder('u')
            ->getQuery();

        // Paginate the results
        $users = $paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number, default to 1
            2 // Number of items per page
        );
        
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
         $this->redirectToRoute('app_login');
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('user1/update/{id}', name: 'user_update1')]
    public function updateUser(Request $request, UserPasswordHasherInterface $userPasswordHasher , UserRepository $userRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $userRepository->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Save the user's roles before handling the form submission
        $originalRoles = $user->getRoles();
    
        $form = $this->createForm(EditType::class, $user);
    
        // Handle the form submission
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the 'roles' field has been modified
            if ($user->getRoles() !== $originalRoles) {
                // If roles have been modified, deny the update and redirect
                $this->addFlash('error', 'You are not allowed to update your role.');
                return $this->redirectToRoute('app_profil');
            }
    
            // Proceed with the rest of the form submission logic
    
            // Get the uploaded file
            $file = $form->get('image')->getData();
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'User updated successfully.');
    
            return $this->redirectToRoute('app_admin');
        }
    
        return $this->renderForm('registration/edit.html.twig', [
            'formA' => $form,
        ]);
    }
    #[Route('/user1/delete/{id}', name: 'user_delete1')]
     public function deleteUser($id, UserRepository $rep, ManagerRegistry $doctrine): Response
     {
         $em= $doctrine->getManager();
         $user= $rep->find($id);
         $em->remove($user);
         $em->flush();
         return $this-> redirectToRoute('app_admin');
     }
}
