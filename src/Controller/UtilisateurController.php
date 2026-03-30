<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\FormCandidatType;
use App\Form\FormEntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route('/choixinscription', name: 'Utilisateur_choixinscription')]
    public function Choixinscription(): Response
    {
        return $this->render('utilisateur/choixinscription.html.twig');
    }

    #[Route('/inscriptionCandidat', name: 'Utilisateur_inscriptioncandidat')]
    public function inscriptionCandidat(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        // Creation d'un nouvel utilisateur
        $candidat = new Utilisateur;
        $candidat->setRoles(['ROLE_CANDIDAT']); // Attribué par default dés l'inscription

        $formCandidat = $this->createForm(FormCandidatType::class, $candidat);
        $formCandidat->handleRequest($request);

        if ($formCandidat->isSubmitted() && $formCandidat->isValid()) {
            // hashage du mot de passe 
            $candidat->setPassword($hasher->hashPassword($candidat, $candidat->getPassword()));

            // Sauvegarder les données dans la base de données
            $entityManager->persist($candidat);
            $entityManager->flush();

            // Notification de succès
            $this->addFlash('success', 'Votre compte a bien été crée !');

            // Redirection page de connexion
            return $this->redirectToRoute('security_login');
        }
        // Affichage du formulaire d'inscription candidat
        return $this->render('utilisateur/forminscriptioncandidat.html.twig', [
            'formcandidat' => $formCandidat
        ]);
    }

    #[Route('/inscriptionEntreprise', name: 'Utilisateur_inscriptionentreprise')]
    public function inscriptionEntreprise(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        // Creation d'un nouvel utilisateur
        $entreprise = new Utilisateur;
        $entreprise->setRoles(['ROLE_ENTREPRISE']); // Attribué par default dés l'inscription

        $formEntreprise = $this->createForm(FormEntrepriseType::class, $entreprise);
        $formEntreprise->handleRequest($request);

        if ($formEntreprise->isSubmitted() && $formEntreprise->isValid()) {
            // hashage du mot de passe 
            $entreprise->setPassword($hasher->hashPassword($entreprise, $entreprise->getPassword()));

            // Sauvegarder les données dans la base de données
            $entityManager->persist($entreprise);
            $entityManager->flush();

            // Notification de succès
            $this->addFlash('success', 'Votre compte a bien été crée !');

            // Redirection page de connexion
            return $this->redirectToRoute('security_login');
        }
        // Affichage du formulaire d'inscription candidat
        return $this->render('utilisateur/forminscriptionentreprise.html.twig', [
            'formentreprise' => $formEntreprise
        ]);
    }
}
