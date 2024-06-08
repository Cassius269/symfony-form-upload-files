<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {

        $form = $this->createFormBuilder()
            ->add('firstname', TextType::class, [
                'label' => 'Prénom:'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom:'
            ])
            ->add('cv', FileType::class, [
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récuperer le fichier
            $file = $form["cv"]->getData();
            $fileName = bin2hex(random_bytes(10));
            $extension = $file->guessExtension() ?? "bin";
            $extensionsList = ["png", "jpg", "jpeg"];

            // Vérifier si le fichier a la bonne extension, si oui déplacer dans le dossier "/public/images"
            if (in_array($extension, $extensionsList, true)) {
                $file->move('images', $fileName . '.' . $extension);
            }
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
