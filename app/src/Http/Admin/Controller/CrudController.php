<?php

namespace App\Http\Admin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends BaseController
{
    protected string $entity;
    protected string $templatePath;
    protected string $routePrefix;
    protected string $formType;
    protected int $perPage = 10;

    public function __construct(
        protected EntityManagerInterface $em,
        protected PaginatorInterface $paginator,
    ) {}

    protected function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entity);
    }

    /** Hook appelé avant de créer le formulaire (création et édition). */
    protected function prepare(object $entity): void {}

    /** Hook appelé avant l'enregistrement. */
    protected function beforeSave(object $entity): void {}

    protected function crudIndex(Request $request): Response
    {
        $query = $this->getRepository()->createQueryBuilder('e')->orderBy('e.id', \SortDirection::Descending);
        $pagination = $this->paginator->paginate($query, max(1, $request->query->getInt('page', 1)), $this->perPage);

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'items' => $pagination,
            'prefix' => $this->routePrefix,
        ]);
    }

    protected function crudNew(Request $request): Response
    {
        return $this->crudForm($request, new $this->entity(), 'new');
    }

    protected function crudEdit(Request $request, object $entity): Response
    {
        return $this->crudForm($request, $entity, 'edit');
    }

    protected function crudDelete(Request $request, object $entity): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $entity->getId(), (string) $request->request->get('_token'))) {
            $this->em->remove($entity);
            $this->em->flush();
            $this->addFlash('success', 'Élément supprimé.');
        }

        return $this->redirectToRoute($this->routePrefix . 'index');
    }

    private function crudForm(Request $request, object $entity, string $template): Response
    {
        $this->prepare($entity);
        /** @var FormInterface $form */
        $form = $this->createForm($this->formType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->beforeSave($entity);
            $this->em->persist($entity);
            $this->em->flush();
            $this->addFlash('success', 'Élément enregistré.');

            return $this->redirectToRoute($this->routePrefix . 'edit', ['id' => $entity->getId()]);
        }

        return $this->render("admin/{$this->templatePath}/{$template}.html.twig", [
            'form' => $form,
            'item' => $entity,
            'prefix' => $this->routePrefix,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
    }
}
