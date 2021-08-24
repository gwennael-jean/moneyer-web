<?php

namespace App\Service\Block;

use App\Service\Transfer\Model\LivingWage;
use App\Service\Transfer\Model\TransferCollection;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class LivingWageTableBlock implements BlockServiceInterface
{
    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $content = $this->twig->render($blockContext->getTemplate(), [
            'livingWage' => $blockContext->getSetting('livingWage')
        ]);
        $response->setContent($content);
        return $response;
    }

    public function load(BlockInterface $block): void
    {
        // TODO: Implement load() method.
    }

    public function getCacheKeys(BlockInterface $block): array
    {
        // TODO: Implement getCacheKeys() method.
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => 'blocks/tables/table--living-wage.html.twig',
        ]);

        $resolver->setRequired('livingWage');
        $resolver->setAllowedTypes('livingWage', LivingWage::class);
    }

}
