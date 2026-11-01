<?php


namespace App\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Http\Admin\Form\ProjectType;
use App\Domain\Project\Entity\Project;


class HomeController extends AbstractController
{

    #[Route("/", name: "home")]
    public function home()
    {
        return $this->render('base.html.twig');
    }
}
