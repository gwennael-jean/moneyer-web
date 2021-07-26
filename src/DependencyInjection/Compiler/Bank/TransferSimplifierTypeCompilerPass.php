<?php

namespace App\DependencyInjection\Compiler\Bank;

use App\Service\Transfer\TransferComputer;
use App\Service\Transfer\TransferSimplifier;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransferSimplifierTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(TransferSimplifier::class)) {
            return;
        }

        $definition = $container->findDefinition(TransferSimplifier::class);

        $taggedServices = $container->findTaggedServiceIds('app.transfer_simplifier_type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransferSimplifierType', [new Reference($id)]);
        }
    }

}
