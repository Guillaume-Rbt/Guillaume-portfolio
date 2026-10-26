<?php


namespace App\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Http\Admin\Form\ProjectForm;
use App\Domain\Project\Entity\Project;


class HomeController extends AbstractController
{

    #[Route("/", name: "home")]
    public function home()
    {

        $project = new Project();
        $form = $this->createForm(ProjectForm::class, $project);

        return $this->render('base.html.twig', [
            'form' => $form,
        ]);
    }
}
