<?php


namespace App\Http\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
}
