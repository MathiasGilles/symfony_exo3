<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}")
 */

class NoteController extends AbstractController
{
    /**
     * @Route("/note", name="note")
     */
    public function index(NoteRepository $repo)
    {
        $notes = $repo->findAll();

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/note/new",name="note_new")
     * @Route("/note/edit/{id}",name="note_edit")
     */
    public function new(Note $note = null,Request $request,TranslatorInterface $translator)
    {
        if ($note == null ) {
            $note = new Note();
        }
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(NoteType::class, $note);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note->setCreatedAt(new \DateTime);
            $manager->persist($note);
            $manager->flush();
            $this->addFlash("success",$translator->trans('note.noteAdded'));
        }

        return $this->render('note/note_new.html.twig',[
            'formNote' => $form->createView(),
            'editTitle' => $note->getId() != null,
            'editMode' => $note->getId() != null,
        ]);
    }

    /**
     * @Route("/note/delete/{id}",name="note_delete")
     */
    public function delete(Note $note = null,TranslatorInterface $translator)
    {
        if($note != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($note);
            $manager->flush();

            $this->addFlash("success",$translator->trans('note.noteDelete'));
        }
        else {
            $this->addFlash("danger",$translator->trans('note.noteNotFound'));
        }
        return $this->redirectToRoute('/');
    }
}
