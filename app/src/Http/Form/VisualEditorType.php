<?php


namespace App\Http\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisualEditorType extends AbstractType
{

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'visual_editor';
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->addModelTransformer(
            new CallbackTransformer(
                fn(?array $value): string =>
                json_encode(
                    $value ?? [],
                    JSON_THROW_ON_ERROR
                ),

                fn(?string $value): array =>
                json_decode(
                    $value ?: '[]',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            )
        );
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ): void {
        $view->vars['tag_name'] = $options['tag_name'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'tag_name' => 'visual-editor',
        ]);
    }
}
