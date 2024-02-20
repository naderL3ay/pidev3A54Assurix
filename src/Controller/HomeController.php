<?php

namespace App\Controller;
use App\Form\EditType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route('/homeaccueil', name: 'accueil')]
    public function homeaccueil(): Response
    {
        return $this->render('registration/accueil_home.html.twig');
    }
    #[Route('/profil', name: 'app_profil')]
    public function profile()
    {
        $user = $this->getUser();

        return $this->render('home/profil.html.twig', [
            'user' => $user,
        ]);
    }
     
    #[Route('/user/update/{id}', name: 'user_update')]
    public function updateUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, EntityManagerInterface $entityManager, $id): Response
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
    
            // etc. (remaining form submission logic)
    
            // Persist and flush the changes
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Add a flash message for success
            $this->addFlash('success', 'User updated successfully.');
    
            // Redirect to the profile page
            return $this->redirectToRoute('app_profil');
        }
    
        // Render the edit form if not submitted or not valid
        return $this->renderForm('registration/edit.html.twig', [
            'formA' => $form,
        ]);
    }
    

     #[Route('/user/delete/{id}', name: 'user_delete')]
     public function deleteUser($id, UserRepository $rep, ManagerRegistry $doctrine): Response
     {
         $em= $doctrine->getManager();
         $user= $rep->find($id);
         $em->remove($user);
         $em->flush();
         return $this-> redirectToRoute('home');
     }
}
