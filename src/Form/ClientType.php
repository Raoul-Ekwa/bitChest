<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];
        $isCreation = $options['is_creation'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter an email.']),
                    new Email(['message' => 'Please enter a valid email.']),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a first name.']),
                    new Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a last name.']),
                    new Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone',
                'required' => false,
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Address',
                'required' => false,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Active Account',
                'required' => false,
            ]);

        // Only add password field for editing (creation uses auto-generated password)
        if ($isEdit) {
            $builder->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'first_options' => [
                    'label' => 'New Password',
                    'attr' => ['autocomplete' => 'new-password'],
                    'help' => 'Leave blank to keep current password',
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'The passwords do not match.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                        'minMessage' => 'Password must be at least {{ limit }} characters.',
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'is_edit' => false,
            'is_creation' => false,
        ]);
    }
}
