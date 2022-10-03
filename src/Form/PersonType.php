<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Person;
use App\Entity\Profile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null, [
                'empty_data' => ''
            ])
            ->add('lastname', null, [
                'empty_data' => ''
            ])
            ->add('age', null, [
                'empty_data' => '0'
            ])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('description', TextareaType::class, [
                'attr' => ['style' => 'min-height:200px'],
            ])
            ->add('profile', EntityType::class, [
                'required' => false,
                'class' => Profile::class,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('hobbies', EntityType::class, [
                'expanded' => true,
                'class' => Hobby::class,
                'multiple' => true,
                'label' => false,

            ])
            ->add('job', EntityType::class, [
                'required' => true,
                'class' => Job::class,
                'attr' => [
                    'class' => 'select2 select2-width'
                ]
            ])
            ->add('pic', FileType::class, [
                'label' => 'Picture',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid document.',
                    ])
                ],
            ])
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
