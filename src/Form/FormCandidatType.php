<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormCandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Nom')
        ->add('Prenom')
        ->add('email')
        ->add('Telephone')
        ->add('datedenaissance', DateType::class, [
            'widget' => 'single_text'
        ])
        ->add('Pays')
        ->add('Ville')
        ->add('password', PasswordType::class)
        ->add('CV', FileType::class, [
            'mapped' => false,
            'required' => false
        ])
        ->add('besoin', ChoiceType::class, [
            'choices' => [
                'Insertion Professionnelle' => 'insertion',
                'Oriention/formation' => 'orientation',
            ],
            'multiple' => true,
            'expanded' => true,
        ])
        ->add('recherche',ChoiceType::class, [
            'choices' => [
                'Stage' => 'stage',
                'CDI' => 'cdi',
                'Alternance' => 'alternance',
                'Freelance' => 'freelance',
            ],
            'multiple' => true,
            'expanded' => true,
        ])
        ->add('linkedin')
        ->add('description', TextareaType::class, [
            'required' => false,
            'attr' => [
                'class' => 'form-control no-resize',
                'rows' => 9,
                'style' => 'resize: none;'
            ]
        ])
            ->add('submit', SubmitType::class, [
            'label' => 'S\'inscrire',
            'attr' => [
                'class' => 'w-100 mt-4 btn btn-dark'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
