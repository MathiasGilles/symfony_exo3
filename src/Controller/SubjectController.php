<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/{_locale}")
 */

class SubjectController extends AbstractController
{
    /**
     * @Route("/subject", name="subject")
     */
    public function index(SubjectRepository $repo)
    {
        $subjects = $repo->findAll();

        return $this->render('subject/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * @Route("/subject/new",name="subject_new")
     * @Route("/subject/edit/{id}",name="subject_edit")
     */
    public function new(Subject $subject = null,Request $request,TranslatorInterface $translator)
    {
        if ($subject == null ) {
            $subject = new Subject();
        }
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(SubjectType::class, $subject);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($subject);
            $manager->flush();
            $this->addFlash("success",$translator->trans('subject.subjectRegister'));
        }

        return $this->render('subject/subject_new.html.twig',[
            'formSubject' => $form->createView(),
            'editTitle' => $subject->getId() != null,
            'editMode' => $subject->getId() != null,
        ]);
    }
    
    /**
     * @Route("/subject/delete/{id}",name="subject_delete")
     */
    public function delete(Subject $subject = null,TranslatorInterface $translator)
    {
        if($subject != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($subject);
            $manager->flush();

            $this->addFlash("success",$translator->trans('subject.subjectDelete'));
        }
        else {
            $this->addFlash("danger",$translator->trans('subject.subjectNotFound'));
        }
        return $this->redirectToRoute('subject');
    }

    /**
     * @Route("/task/subject/{id}",name="subject_detail")
     */
    public function detail($id,SubjectRepository $repo){

        $subject = $repo->find($id);

        return $this->render('subject/subject_detail.html.twig',[
            'subject' => $subject,
        ]);
    }
}

