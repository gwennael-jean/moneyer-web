<?php

namespace App\Form\Bank\Charge;

use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Entity\User;
use App\Service\Provider\Bank\AccountProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeFilterType extends AbstractType
{
    public function __construct(
        private AccountProvider $accountProvider
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod(Request::METHOD_GET)
            ->add('name', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Charge Name'
                ]
            ])
            ->add('account', EntityType::class, [
                'required' => false,
                'placeholder' => 'Account',
                'class' => Account::class,
                'choices' => $this->accountProvider->getByUser($options['user']),
                'choice_label' => function (Account $account) use ($options) {
                    return null !== $account->getOwner() && $account->getOwner() !== $options['user']
                        ? sprintf('%s (%s)', $account->getName(), $account->getOwner()->getFullname())
                        : $account->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Charge::class,
            'csrf_protection' => false,
        ]);

        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
