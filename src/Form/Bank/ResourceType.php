<?php

namespace App\Form\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\Resource;
use App\Entity\User;
use App\Service\Provider\Bank\AccountProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    public function __construct(
        private AccountProvider $accountProvider
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
                'choices' => $this->accountProvider->getByUser($options['user']),'choice_label' => function (Account $account) use ($options) {
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
            'data_class' => Resource::class,
        ]);

        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }
}
