<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/{_locale}")
 */

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(UserRepository $repo)
    {
        $users=$repo->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/new",name="user_new")
     * @Route("/user/edit/{id}",name="user_edit")
     */
    public function new(User $user = null,Request $request)
    {
        if ($user == null) {
            $user = new User();
        }
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setCreatedAt(new \DateTime);
            $fichier = $form->get('photo')->getData();
            //Si un fichier a été uploadé
            if ($fichier) {
                // On renomme le fichier
                $nomFicher = uniqid() . '.' . $fichier->guessExtension();
                try {
                    // on essaie de deplacer le fichier
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFicher
                    );
                } catch (FileExeption $e) {
                    $this->addFlash('danger', "Impossible d'uploader le fichier");
                    return $this->redirectToRoute('user');
                }
                $user->setPhoto($nomFicher);
            }
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success","Etudiant ajouté");
        }
       return $this->render('user/user_new.html.twig',[
           "formUser" => $form->createView(),
           'editTitle' => $user->getId() != null,
            'editMode' => $user->getId() != null
       ]); 
    }

    /**
     * @Route("/user/delete/{id}",name="user_delete")
     */
    public function delete(Subject $user = null)
    {
        if($user != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($user);
            $manager->flush();

            $this->addFlash("success",$translator->trans('subject.subjectDelete'));
        }
        else {
            $this->addFlash("danger",$translator->trans('subject.subjectNotFound'));
        }
        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/user/detail/{id}",name="user_detail")
     */
    public function detail($id,UserRepository $repo)
    {
        $user = $repo->find($id);

        return $this->render('user/user_detail.html.twig',[
            'user' => $user,
        ]);
    }
}
