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
    public function inscriptionCandidat(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $hasher): Response 
    {
        // Creation d'un nouvel utilisateur
        $candidat = new Utilisateur;
        $candidat->setRoles(['ROLE_CANDIDAT']);

        $formCandidat = $this->createForm(FormCandidatType::class, $candidat);
        $formCandidat->handleRequest($request);

        if ($formCandidat->isSubmitted() && $formCandidat->isValid()) {

            //  Hash du mot de passe
            $candidat->setPassword(
                $hasher->hashPassword($candidat, $candidat->getPassword())
            );

            //  Gestion du fichier CV
            $cvFile = $formCandidat->get('CV')->getData();

            if ($cvFile) {
                $newFilename = uniqid() . '.' . $cvFile->guessExtension();

                try {
                    $cvFile->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du CV');
                }

                // On enregistre le nom du fichier en base
                $candidat->setCV($newFilename);
            }

            //  Sauvegarde en base
            $entityManager->persist($candidat);
            $entityManager->flush();

            //  Message succès
            $this->addFlash('success', 'Votre compte a bien été créé !');

            return $this->redirectToRoute('security_login');
        }

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
    #[Route('/espaceCandidat', name: 'Utilisateur_espaceCandidat')]
    public function espaceCandidat(): Response
    {
        return $this->render('utilisateur/Candidat/espaceCandidat.html.twig');
    }
    #[Route('/espaceEntreprise', name: 'Utilisateur_espaceEntreprise')]
    public function espaceEntreprise(): Response
    {
        return $this->render('utilisateur/Entreprises/espaceEntreprise.html.twig');
    }

}
