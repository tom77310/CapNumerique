<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormEntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Nom')
        ->add('SIRET')
        ->add('email')
        ->add('Telephone')
        ->add('Pays')
        ->add('Ville')
        ->add('password')
        ->add('Secteur')
        ->add('taille')
        ->add('site')
        ->add('linkedin')
        ->add('description', TextareaType::class, [
            'required' => false,
            'attr' => [
                'class' => 'form-control no-resize',
                'rows' => 8,
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
