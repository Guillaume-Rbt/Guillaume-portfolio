<?php

namespace App\Http\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Http\Form\VisualEditorType;
use App\Domain\Project\Entity\Translation\ProjectTranslation;

class ProjectTranslationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'post_form.title'])
            ->add('locale', TextType::class, ['label' => 'post_form.locale'])
            ->add('excerpt', TextType::class, ['label' => 'post_form.excerpt'])
            ->add('content', VisualEditorType::class, [
                'label' => 'post_form.content',
                'mapped' => true,
                'attr' => ['shown' => false],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProjectTranslation::class]);
    }
}
