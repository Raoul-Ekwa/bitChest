<?php

namespace App\Form;

use App\Entity\Cryptocurrency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CryptocurrencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('symbol', TextType::class, [
                'label' => 'Symbol',
                'constraints' => [
                    new NotBlank(message: 'Please enter a symbol.'),
                    new Length(max: 10),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(message: 'Please enter a name.'),
                    new Length(max: 100),
                ],
            ])
            ->add('currentPrice', TextType::class, [
                'label' => 'Current Price (EUR)',
                'constraints' => [
                    new NotBlank(message: 'Please enter a price.'),
                    new Regex(
                        pattern: '/^\d+(\.\d{1,8})?$/',
                        message: 'Enter a valid positive decimal number.'
                    ),
                ],
            ])
            ->add('image', TextType::class, [
                'label' => 'Image URL',
                'required' => false,
                'constraints' => [
                    new Length(max: 255),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cryptocurrency::class,
        ]);
    }
}
