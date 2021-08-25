<?php

namespace App\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank\Account;
use App\Entity\User;
use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\Model\TransferCollection;
use Doctrine\Common\Collections\ArrayCollection;

class TransferComputer
{
    private ArrayCollection $transferChargeDistributions;

    public function __construct()
    {
        $this->transferChargeDistributions = new ArrayCollection();
    }

    public function getTransferChargeDistribution(string $type): TransferChargeDistribution
    {
        return $this->transferChargeDistributions->get($type);
    }

    public function addTransferChargeDistribution(TransferChargeDistribution $transferChargeDistribution): self
    {
        $this->transferChargeDistributions->set($transferChargeDistribution->getType(), $transferChargeDistribution);

        return $this;
    }

    public function compute(ArrayCollection $accounts, ?User $user = null): TransferCollection
    {
        $transfers = new TransferCollection($accounts);

        foreach ($accounts as $account) {
            foreach ($account->getCharges($user) as $charge) {
                $type = null !== $charge->getChargeDistribution()
                    ? $charge->getChargeDistribution()->getType()
                    : ChargeDistributionType::VIEW;

                $transferChargeDistribution = $this->getTransferChargeDistribution($type);

                $transferChargeDistribution->execute($transfers, $charge);
            }
        }

        $continue = true;

        while ($continue && !$transfers->getPot()->isEmpty()) {
            $continue = false;

            $userPayer = $transfers->getPot()->getFirstPayer();

            if (null !== $userPayer) {

                $userReceiver = $transfers->getPot()->getFirstReceiver();

                if (null !== $userReceiver) {
                    $continue = true;

                    $accountToPay = $this->getAccountToPay($accounts, $userPayer);
                    $accountToReceive = $this->getAccountToReceive($accounts, $transfers, $userReceiver);

                    $amount = $transfers->getPot()->get($userPayer) <= abs($transfers->getPot()->get($userReceiver))
                        ? $transfers->getPot()->get($userPayer)
                        : abs($transfers->getPot()->get($userReceiver));

                    $transfer = new Transfer($accountToPay ?? $userPayer, $accountToReceive, $amount);
                    $transfers->add($transfer);

                    if (null !== $accountToPay) {
                        $transfers->getAccountBalances()->remove($accountToPay, $amount);
                    }

                    $transfers->getPot()->remove($userPayer, $amount);

                    $transfers->getAccountBalances()->add($accountToReceive, $amount);
                    $transfers->getPot()->add($userReceiver, $amount);
                } else {
                    $debitedAccount = $this->getDebitedAccount($transfers);

                    if (null !== $debitedAccount) {
                        $continue = true;

                        $accountToPay = $this->getAccountToPay($accounts, $userPayer);

                        $amount = $transfers->getPot()->get($userPayer) <= abs($transfers->getAccountBalances()->get($debitedAccount))
                            ? $transfers->getPot()->get($userPayer)
                            : abs($transfers->getAccountBalances()->get($debitedAccount));

                        $transfer = new Transfer($accountToPay, $debitedAccount, $amount);
                        $transfers->add($transfer);

                        $transfers->getAccountBalances()->remove($accountToPay, $amount);
                        $transfers->getPot()->remove($userPayer, $amount);

                        $transfers->getAccountBalances()->add($debitedAccount, $amount);
                    }
                }
            }
        }

        return $transfers;
    }

    private function getDebitedAccount(TransferCollection $transfers): ?Account
    {
        foreach ($transfers->getAccountBalances()->map() as $account => $amount) {
            if ($amount < 0) {
                return $account;
            }
        }

        return null;
    }

    private function getAccountToPay(ArrayCollection $accounts, User $user): ?Account
    {
        $userAccounts = $accounts->filter(function (Account $account) use ($user) {
            return $account->getOwner() === $user;
        });

        $mainAccount = null;
        foreach ($userAccounts as $account) {
            if (null === $mainAccount || $account->getTotalResources() > $mainAccount->getTotalResources()) {
                $mainAccount = $account;
            }
        }

        return $mainAccount;
    }

    private function getAccountToReceive(ArrayCollection $accounts, TransferCollection $transfers, User $user): Account
    {
        $userAccounts = $accounts->filter(function (Account $account) use ($user) {
            return $account->getOwner() === $user;
        });

        $mainAccount = null;
        foreach ($userAccounts as $account) {
            if (null === $mainAccount || $transfers->getAccountBalances()->get($account) < $transfers->getAccountBalances()->get($mainAccount)) {
                $mainAccount = $account;
            }
        }

        return $mainAccount;
    }
}
