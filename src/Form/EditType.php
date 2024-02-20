<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email')
        ->add('firstname')
        ->add('lastname')
       // ->add('date_d_n')
        ->add('tel_num')
        ->add('adresse')
        ->add('image', FileType::class, [
            'label' => 'Choose an image',
            'mapped' => false, // To handle file upload manually in the controller
            'required' => false, // Allow empty uploads
        ])
        ->add('roles', ChoiceType::class, [
            'label' => 'Select Roles',
            'choices' => [
                'Client' => 'ROLE_CLIENT',
                'EXPERT' => 'ROLE_EMPLOYEE',
                // Add other roles as needed
            ],
            'multiple' => true,
            'expanded' => false, // Change to true if you want checkboxes instead of a drop-down
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ])
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
