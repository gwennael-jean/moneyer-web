<?php

namespace App\Form\Bank\Account;

use App\Entity\Bank\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod(Request::METHOD_GET)
            ->add('name', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Account Name'
                ]
            ])
            ->add('owner', null, [
                'required' => false,
                'placeholder' => 'Account Owner',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
