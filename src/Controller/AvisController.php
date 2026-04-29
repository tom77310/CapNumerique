<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisFormType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvisController extends AbstractController
{
    // Page pour voir les avis, avec un formulaire pour ajouter un avis
    #[Route('/avis', name: 'Avis_AffichageAvis')]
    public function Avis(Request $request, EntityManagerInterface $em, AvisRepository $avisRepository): Response
    {
        $avis = new Avis();
        if ($this->getUser()) {
            $form = $this->createForm(AvisFormType::class, $avis);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $avis->setUtilisateur($this->getUser());
                $avis->setCreatedAt(new \DateTimeImmutable());

                $em->persist($avis);
                $em->flush();

                $this->addFlash('success', 'Avis ajouté !');
                return $this->redirectToRoute('Avis_AffichageAvis');
            }
        } else {
            $form = null;
        }
        return $this->render('avis/DonnerSonAvis.html.twig', [
            'avisListe' => $avisRepository->findBy([], ['id' => 'DESC']),
            'form' => $form?->createView()
        ]);
    }

    // Modifier son avis
    #[Route('/avis/modifiersonavis/{id}', name: 'Avis_ModifierAvis')]
    public function ModifierAvis(Avis $avis, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser() !== $avis->getUtilisateur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $form = $this->createForm(AvisFormType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em-> flush();
            return $this->redirectToRoute('Avis_AffichageAvis');
        }
        return $this->render('avis/modifierAvis.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Supprimer son avis
    #[Route('/supprimeravis/{id}', name: 'Avis_SupprimerAvis')]
    public function SupprimerAvis(Avis $avis, EntityManagerInterface $em): Response
    {
        if ($this->getUser() !== $avis->getUtilisateur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($avis);
        $em->flush();

        return $this->redirectToRoute('Avis_AffichageAvis');
    }
}