<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class SellTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $maxQuantity = $options['max_quantity'];

        $builder
            ->add('quantity', NumberType::class, [
                'label' => 'Quantity to Sell',
                'scale' => 8,
                'html5' => true,
                'attr' => [
                    'step' => '0.00000001',
                    'min' => '0.00000001',
                    'max' => $maxQuantity,
                    'placeholder' => '0.00000000',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a quantity.']),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Quantity must be greater than 0.',
                    ]),
                    new LessThanOrEqual([
                        'value' => (float) $maxQuantity,
                        'message' => 'You cannot sell more than {{ compared_value }} units.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'max_quantity' => '0',
        ]);
    }
}
