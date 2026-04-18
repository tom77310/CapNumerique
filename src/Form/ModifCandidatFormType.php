<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifCandidatFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
        ->add('sexe', TextType::class)
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('email', EmailType::class)
        ->add('telephone', TextType::class)
        ->add('pays', TextType::class)
        ->add('ville', TextType::class)
        ->add('linkedin', TextType::class, [
            'required' => false
        ])
        ->add('description', TextareaType::class, [
            'required' => false
        ])

        // 📅 Date de naissance
        ->add('datedenaissance', DateType::class, [
            'widget' => 'single_text',
            'required' => false,
            'label' => 'Date de naissance'
        ])

        // 📄 CV (non mappé)
        ->add('cv', FileType::class, [
            'mapped' => false,
            'required' => false,
            'label' => 'Nouveau CV'
        ])

        // 🧠 besoins
        ->add('besoin', ChoiceType::class, [
            'choices' => [
                'Insertion' => 'Insertion',
                'Orientation' => 'Orientation',
            ],
            'multiple' => true,
            'expanded' => true, // checkbox
            'label' => false
        ])

        // 🔎 recherche
        ->add('recherche', ChoiceType::class, [
            'choices' => [
                'Stage' => 'Stage',
                'CDI' => 'CDI',
                'Alternance' => 'Alternance',
                'Freelance' => 'Freelance',
            ],
            'multiple' => true,
            'expanded' => true,
            'label' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
