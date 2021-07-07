<?php

namespace App\Form\Bank;

use App\Entity\Bank\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
        ;

        if ($options['form_type'] === 'all') {
            $builder
                ->add('owner')
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'form_type' => 'all',
        ]);

        $resolver->setRequired('form_type');
        $resolver->setAllowedValues('form_type', ['simple', 'all']);
    }
}
