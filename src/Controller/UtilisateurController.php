<?php

namespace App\Controller;

use App\Entity\CarteTrello;
use App\Entity\Utilisateur;
use App\Form\ChangePasswordCandidatType;
use App\Form\FormCandidatType;
use App\Form\FormEntrepriseType;
use App\Form\ModifCandidatFormType;
use App\Repository\CarteTrelloRepository;
use App\Repository\ColonneTrelloRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
    public function inscriptionCandidat(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $hasher, ColonneTrelloRepository $colRepo, CarteTrelloRepository $carteTrelloRepo): Response 
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
            $nomCandidat = strtoupper($candidat->getNom()); // On récupere le nom du candidat
            $prenomCandidat = ucfirst($candidat->getPrenom()); // On récupère le prenom du candidat
            $dateupload = (new \DateTime())->format('d-m-Y'); // Date d'upload format j/m/a
            if ($cvFile) {
                $nomFichier = 'CV -' . $nomCandidat . '_' . $prenomCandidat . '_' . $dateupload;

                $extension = $cvFile->guessExtension();
                $nomfichierComplet = $nomFichier . '.' . $extension;

                try {
                    $cvFile->move(
                        $this->getParameter('cv_directory'),
                        $nomfichierComplet
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du CV');
                }

                // On enregistre le nom du fichier en base
                $candidat->setCV($nomfichierComplet);
            }

            //  Sauvegarde en base
            $entityManager->persist($candidat);
            $entityManager->flush();

            // Creation de carte a l'inscription
            // 🔹 Récupérer la colonne "A Faire"
            $colonne = $colRepo->findOneBy(['titre' => 'A Faire']);

            if ($colonne) {

                // ❗ Anti doublon (sécurité)
                $existe = $carteTrelloRepo->findOneBy(['utilisateur' => $candidat]);

                if (!$existe) {

                    // 🔹 Récupérer la dernière position
                    $dernieresCartes = $carteTrelloRepo->findBy(
                        ['colonne' => $colonne],
                        ['position' => 'DESC'],
                        1
                    );

                    $position = $dernieresCartes
                        ? $dernieresCartes[0]->getPosition() + 1
                        : 1;

                    // 🔹 Création de la carte
                    $carte = new CarteTrello();
                    $carte->setTitre('Carte');
                    $carte->setUtilisateur($candidat);
                    $carte->setColonne($colonne);
                    $carte->setPosition($position);

                    $entityManager->persist($carte);
                    $entityManager->flush();
                }
            }

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
    // Profil candidat
        // Affichage des infos candidat
        #[Route('/espaceCandidat/profil', name: 'Utilisateur_ProfilCandidat')]
        public function profilCandidat(): Response
        {
            $this->denyAccessUnlessGranted('ROLE_CANDIDAT'); // Autorise uniquement les utilisateurs avec le role ROLE_CANDIDAT
            $candidat = $this->getUser();

            return $this->render('utilisateur/Candidat/profilCandidat.html.twig', [
                'candidat' => $candidat,
            ]);
        }
        // Modif Infos Candidat
        #[Route('/espaceCandidat/profil/modifiermonprofil', name: 'Utilisateur_modifierProfilCandidat')]
        public function modifierProfilCandidat(Request $request, EntityManagerInterface $em): Response
        {
            /** @var \App\Entity\Utilisateur $candidat */ // Garder pour éviter que VSCode ne souligne les methode setCV et getCV
            $candidat = $this->getUser();
            $formmodifcandidat = $this->createForm(ModifCandidatFormType::class, $candidat);
            $formmodifcandidat->handleRequest($request);

            if ($formmodifcandidat->isSubmitted() && $formmodifcandidat->isValid()) {
                // Gestion du CV
                $cvFile = $formmodifcandidat->get('cv')->getData();

                if ($cvFile) {

                    // 🧹 Supprimer l'ancien CV
                    if ($candidat->getCV()) {

                        $ancienCV = $this->getParameter('kernel.project_dir') 
                            . '/public/uploads/cv/' 
                            . $candidat->getCV();

                        if (file_exists($ancienCV)) {
                            unlink($ancienCV);
                        }
                    }

                    // 📛 Nettoyer nom/prénom
                    $nomCandidat = strtolower(str_replace(' ', '_', $candidat->getNom()));
                    $prenomCandidat = strtolower(str_replace(' ', '_', $candidat->getPrenom()));

                    // 📅 date
                    $dateupload = (new \DateTime())->format('d-m-Y');

                    // 📄 extension
                    $extension = $cvFile->guessExtension();

                    // 🏷️ Nouveau nom
                    $newFileName = "CV-{$nomCandidat}_{$prenomCandidat}_{$dateupload}.{$extension}";

                    // 📁 Upload
                    $cvFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/cv/',
                        $newFileName
                    );

                    // 💾 Sauvegarde en base
                    $candidat->setCV($newFileName);
                }
                $em->flush();
                return $this->redirectToRoute('Utilisateur_ProfilCandidat');
            }
            return $this->render('utilisateur/Candidat/modifierProfilCandidat.html.twig', [
                'formmodifcandidat' => $formmodifcandidat->createView(),
            ]);
        }
        // Modifier le mot de passe Candidat uniquement
        #[Route('/espaceCandidat/profil/modifiermotdepasse', name: 'Utilisateur_modifierMotDePasse')]
        public function modifierMotDePasseCandidat(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
        {
            /** @var \App\Entity\Utilisateur $candidat */ // Garder pour éviter un bug visuel VSCode
            $candidat = $this->getUser();

            $form = $this->createForm(ChangePasswordCandidatType::class); // Formulaire modif mot de passe
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $Ancienmdp = $form->get('Ancienmdp')->getData();
                $Nouveaumdp = $form->get('Nouveaumdp')->getData();

                // Vérification du mot de passe actuel
                if (!$passwordHasher->isPasswordValid($candidat, $Ancienmdp)) {
                    $this->addFlash('danger', 'Mot de passe actuel incorrect');
                } elseif ($Nouveaumdp) {
                    // Hash du nouveau mot de passe
                    $hashedPassword = $passwordHasher->hashPassword($candidat, $Nouveaumdp);
                    $candidat->setPassword($hashedPassword);

                    $em->flush();

                    $this->addFlash('success', 'Mot de passe modifié avec succès');

                    return $this->redirectToRoute('Utilisateur_ProfilCandidat');
                } else {
                    $this->addFlash('warning', 'Veuillez saisir un nouveau mot de passe');
                }
            }

            return $this->render('utilisateur/Candidat/modifierMotDePasseCandidat.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        // Supprimer le compte Candidat
        #[Route('/espaceCandidat/profil/supprimercomptecandidat', name: 'Utilisateur_SupprimerCompteCandidat', methods: ['POST'])]
        public function SupprimerCompteCandidat(Request $request, EntityManagerInterface $em, Security $security): Response
        {
            /** @var \App\Entity\Utilisateur $candidat */ // Ne pas enlever pour ne pas avoir de bug visuel de l'IDE
            $candidat = $this->getUser();

            // Sécurité : Verification CSRF
            if (!$this->isCsrfTokenValid('suppressioncomptecandidat', $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF invalide');
            }

            // Suppression du CV si il existe
            if ($candidat->getCV()) {
                $file = $this->getParameter('kernel.project_dir').'/public/uploads/cv/'.$candidat->getCV();
                // Si le fichier existe, on le supprime
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Suppression de la carte trello
            $cartes = $em->getRepository(CarteTrello::class)->findBy(['utilisateur' => $candidat]);

            // tant qu'une carte existe et est associé a un utilisateur, on la supprime
            foreach ($cartes as $carte){
                $em->remove($carte);
            }

            // Deconnexion avant la suppresion
            $security->logout(false);
            // Suppression de l'utilisateur
            $em->remove($candidat);
            $em->flush();

            // redirection apres suppression
            return $this->redirectToRoute('app_default');
        }
        
        // Page pour prendre RDV Candidat
        #[Route('/espaceCandidat/profil/prendrerdv', name: 'Utilisateur_RDVCandidat')]
        public function CandidatRDV(): Response
        {
            return $this->render('utilisateur/Candidat/rendezvouscandidat.html.twig');
        }

    #[Route('/espaceEntreprise', name: 'Utilisateur_espaceEntreprise')]
    public function espaceEntreprise(): Response
    {
        return $this->render('utilisateur/Entreprises/espaceEntreprise.html.twig');
    }



    // Pages dans la barre de nav
    // Orientation Pro
    #[Route('/orientationpro', name: 'Utilisateur_orientationpro')]
    public function orientationPro(): Response
    {
        return $this->render('utilisateur/orientationpro.html.twig');
    }
    // Insertion Pro
    #[Route('/insertionpro', name: 'Utilisateur_insertionpro')]
    public function insertionPro(): Response
    {
        return $this->render('utilisateur/insertionpro.html.twig');
    }
    // Metiers de la tech
    #[Route('/metiersdelatech', name: 'Utilisateur_metierstech')]
    public function metiersDeLaTech(): Response
    {
        return $this->render('utilisateur/metierstech.html.twig');
    }
}