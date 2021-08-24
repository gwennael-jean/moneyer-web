<?php

namespace App\Form\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\ChargeDistribution;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeDistributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'choices' => $this->getUsers($options['accounts']),
                'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined("accounts");

        $resolver->setDefaults([
            'data_class' => ChargeDistribution::class,
            'accounts' => null,
        ]);

        $resolver->setAllowedTypes("accounts", ["null", ArrayCollection::class]);
    }

    private function getUsers(?ArrayCollection $accounts = null): ArrayCollection
    {
        $users = new ArrayCollection();

        if (null !== $accounts) {
            /** @var Account $account */
            foreach ($accounts as $account) {
                if (null !== $account->getOwner() && !$users->contains($account->getOwner())) {
                    $users->add($account->getOwner());
                }
            }
        }

        return $users;
    }
}
