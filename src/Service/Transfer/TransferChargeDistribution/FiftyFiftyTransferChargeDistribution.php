<?php

namespace App\Service\Transfer\TransferChargeDistribution;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\Bank\Charge;
use App\Entity\User;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Transfer\TransferChargeDistribution;
use Doctrine\Common\Collections\ArrayCollection;

class FiftyFiftyTransferChargeDistribution extends TransferChargeDistribution
{
    public function __construct(
        private AccountProvider $accountProvider
    )
    {
    }

    public function getType(): string
    {
        return ChargeDistributionType::FIFTY_FIFTY;
    }

    public function execute(Charge $charge, ArrayCollection $transfers): void
    {
        $users = $charge->getChargeDistribution()->getUsers();
        $users->add($charge->getAccount()->getOwner());

        $amount = $charge->getAmount() / count($users);

        /** @var User $user */
        foreach ($users as $user) {
            $creditedAccounts = $this->accountProvider->getByUser($user)
                ->filter(fn (Account $account) => $account->getTotal() >= $amount);

            foreach ($creditedAccounts as $account) {
                $this->getAccountWeakMap()->addCreditedAccount($account);
            }
        }

        foreach ($users as $user) {
            $creditedAccount = $this->findCreditedAccount($charge, [
                'user' => $user,
                'amount' => $amount,
            ]);

            if (null !== $creditedAccount) {
                $this->transferProcess($creditedAccount, $charge, $transfers, $amount);
            }
        }
    }

    protected function creditedAccountfilter(Account $account, Charge $charge, array $options = []): bool
    {
        $user = $options['user'];
        $amount = $options['amount'];

        if ($account->getOwner() === $user) {
            $creditedAccountDto = $this->getAccountWeakMap()->getCreditedAccountDto($account);
            return $creditedAccountDto->getTotal() >= $amount;
        }

        return false;
    }
}
