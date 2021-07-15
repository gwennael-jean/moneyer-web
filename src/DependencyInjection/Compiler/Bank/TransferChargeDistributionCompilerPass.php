<?php

namespace App\DependencyInjection\Compiler\Bank;

use App\Service\Transfer\TransferComputer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransferChargeDistributionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(TransferComputer::class)) {
            return;
        }

        $definition = $container->findDefinition(TransferComputer::class);

        $taggedServices = $container->findTaggedServiceIds('app.transfer_charge_distribution');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransferChargeDistribution', [new Reference($id), $id]);
        }
    }

}