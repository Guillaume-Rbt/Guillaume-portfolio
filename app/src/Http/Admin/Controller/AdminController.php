<?php

namespace App\Http\Admin\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{

    #[Route('/admin', name: 'admin_dashboard')]
    public function index()
    {
        return $this->render('admin/base.html.twig');
    }
}
