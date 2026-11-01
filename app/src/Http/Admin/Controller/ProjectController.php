<?php

namespace App\Http\Admin\Controller;

use App\Domain\Project\Entity\Project;
use App\Http\Admin\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/project', name: 'admin_project_')]
class ProjectController extends CrudController
{
    protected string $entity = Project::class;
    protected string $templatePath = 'project';
    protected string $routePrefix = 'admin_project_';
    protected string $formType = ProjectType::class;

    public function __construct(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        private readonly SluggerInterface $slugger,
        #[Autowire('%app.locales%')] private readonly array $locales,
    ) {
        parent::__construct($em, $paginator);
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->crudIndex($request);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->crudNew($request);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project): Response
    {
        return $this->crudEdit($request, $project);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Project $project)
    {
        return $this->crudDelete($request, $project);
    }

    protected function prepare(object $entity): void
    {
        $entity->initTranslations($this->locales);
    }

    protected function beforeSave(object $entity): void
    {
        foreach ($entity->getTranslations()->toArray() as $translation) {
            if ($translation->isEmpty()) {
                $entity->removeTranslation($translation);
                continue;
            }
            $translation->setSlug(strtolower($this->slugger->slug($translation->getTitle())));
        }
    }
}
