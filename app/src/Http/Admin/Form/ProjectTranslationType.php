<?php

namespace App\Http\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Http\Form\VisualEditorType;
use App\Domain\Project\Entity\Translation\ProjectTranslation;

class ProjectTranslationType extends AbstractType
{
    public function __construct(private readonly RequestStack $requestStack) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('locale', HiddenType::class)
            ->add('title', TextType::class, ['label' => 'post_form.title', 'empty_data' => '', 'required' => false])
            ->add('excerpt', TextType::class, ['label' => 'post_form.excerpt', 'empty_data' => '', 'required' => false])
            ->add('content', VisualEditorType::class, [
                'label' => 'post_form.content',
                'required' => false,
                'mapped' => true,
                'attr' => ['shown' => false],
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $translation = $form->getData();
        if ($translation instanceof ProjectTranslation && $translation->getLocale()) {
            $view->vars['label'] = ucfirst(\Locale::getDisplayLanguage($translation->getLocale(), $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr'));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProjectTranslation::class]);
    }
}
