<?php

namespace App\Form\Bank\Charge;

use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Entity\User;
use App\Service\Provider\Bank\AccountProviderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeType extends AbstractType
{
    public function __construct(
        private AccountProviderInterface $accountProvider
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('amount')
            ->add('account', EntityType::class, [
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
        ]);

        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }
}
