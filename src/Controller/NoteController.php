<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NoteController extends AbstractController
{
    /**
     * @Route("/note", name="note")
     */
    public function index(NoteRepository $repo)
    {
        $notes = $repo->fidnAll();

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/note/new",name="note_new")
     * @Route("/note/edit/{id}",name="note_edit")
     */
    public function new(Note $note = nul,Request $request)
    {
        if ($note = null ) {
            $note = new Note();
        }
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(NoteType::class, $note);
        $form -> handleRequest($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $note->setCreatedAt(new \DateTime);
            $form->persist($user);
            $form->flush();
            $this->addFlash("succes","Note enregistrée");
        }

        return $this->render('note/note_new.html.twig',[
            'formNote' => $form->createView(),
            'editTitle' => $user->getId() != null,
            'editMode' => $user->getId() != null,
        ]);
    }

    /**
     * @Route("/note/delete/{id}",name="note_delete")
     */
    public function delete(Note $note = null)
    {
        if($note != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($note);
            $manager->flush();

            $this->addFlash("success","Note supprimée");
        }
        else {
            $this->addFlash("danger","Note introuvable");
        }
        return $this->redirectToRoute('/');
    }
}
