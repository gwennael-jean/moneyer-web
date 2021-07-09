<?php

namespace App\Form\Bank;

use App\DataTransformer\EmailToUserTransformer;
use App\Entity\Bank\AccountShare;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountShareType extends AbstractType
{
    public function __construct(
        private EmailToUserTransformer $emailToUserTransformer
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EmailType::class)
            ->add('type')
        ;

        $builder->get('user')->addModelTransformer($this->emailToUserTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AccountShare::class,
        ]);
    }
}
