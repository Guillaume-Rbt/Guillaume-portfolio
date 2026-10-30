<?php

namespace App\Http\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Http\Admin\Form\ProjectTranslationType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Domain\Project\Entity\Project;

class ProjectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'post_form.title'])
            ->add('image', TextType::class, ['label' => 'post_form.image'])
            ->add('translations', CollectionType::class, [
                'entry_type' => ProjectTranslationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Project::class]);
    }
}
