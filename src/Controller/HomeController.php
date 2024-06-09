<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
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
            ->add('lastname', TextType::class, [
                'label' => 'Prénom:'
            ])
            ->add('cv', FileType::class, [

                'multiple' => true,
                'constraints' => [
                    new All(new File(
                        [
                            'maxSize' => '2M',
                            'maxSizeMessage' => 'Taille maximale atteinte pour {{ name }} et la taille limite est de {{ limit }} {{ suffix }}',
                            'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg'],
                            'mimeTypesMessage' => 'Veuillez soumettre une image'
                        ]
                    ))
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récuperer le fichier
            $attachements = $form["cv"]->getData();

            $extensionsList = ["png", "jpg", "jpeg"];

            foreach ($attachements as $file) {
                $fileName = bin2hex(random_bytes(10)); // créer un nom de fichier aléatoire, sécurisé et très probablement unique
                $extension = $file->guessExtension() ?? "bin";
                // Vérifier si le fichier a la bonne extension, si oui déplacer dans le dossier "/public/images"
                if (in_array($extension, $extensionsList, true)) {

                    $file->move('images', $fileName . '.' . $extension);
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
