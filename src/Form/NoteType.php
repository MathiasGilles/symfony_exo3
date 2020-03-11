<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Subject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value')
            ->add('subject',EntityType::class,[
                'class' => Subject::class,
                'choice_label' => function($subject){
                    $display = $subject->getName() . " | " . "Coeff : " . $subject->getCoefficient();
                    return $display;
                },
            ])
            ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
