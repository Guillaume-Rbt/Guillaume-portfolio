<?php

namespace App\Http\Admin\Form;

use App\Domain\Project\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', TextType::class, ['label' => 'post_form.image', 'empty_data' => '', 'required' => false])
            ->add('translations', CollectionType::class, [
                'label' => 'post_form.translations',
                'entry_type' => ProjectTranslationType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'save', "attr" => ["class" => "btn btn-primary"]]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'constraints' => [new Callback(function (Project $project, ExecutionContextInterface $context): void {
                foreach ($project->getTranslations() as $translation) {
                    if (trim($translation->getTitle()) !== '') {
                        return;
                    }
                }
                $context->buildViolation('Au moins une traduction avec un titre est requise.')->addViolation();
            })],
        ]);
    }
}
