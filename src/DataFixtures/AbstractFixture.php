<?php

namespace App\DataFixtures;

use App\Entity\Media;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use App\Kernel;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of AbstractFixture
 *
 * @author gjean
 */
abstract class AbstractFixture extends Fixture implements ContainerAwareInterface, FixtureGroupInterface
{
    protected ContainerInterface $container;

    protected Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public static function getGroups(): array
    {
        return ['initial'];
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function getData()
    {
        $data = $this->container->getParameter('fixture');
        $data['data_folder'] = $data['data_folder'] ?? 'data';

        return Yaml::parse(file_get_contents(implode(DIRECTORY_SEPARATOR, [
            $this->getAssetsPath(),
            $data['data_folder'],
            trim($this->getYamlPath())
        ])));
    }

    protected function getDateTime($timestamp)
    {
        $date = new DateTime();

        if ('now' !== $timestamp) {
            if (preg_match('/^now:(.*)/', $timestamp, $match)) {
                $date->modify($match[1]);
            } else {
                $date->setTimestamp($timestamp);
            }
        }

        return $date;
    }

    protected function getReferenceEntity($fixture, $id)
    {
        return $this->getReference($this->getReferencePath($fixture, $id));
    }

    protected function getReferencePath($fixture, $id)
    {
        return $fixture . $id;
    }

    protected function getRandomFileInDirectory($directory): File
    {
        $directory = $this->getAssetsPath() . '/media/random/' . ltrim($directory, '/');

        $finder = new Finder();
        $finder->files()->in($directory);

        if ($finder->count() === 0) {
            throw new \Exception('No random file exist in ' . $directory);
        }

        $rand = rand(0, $finder->count() - 1);

        $i = 0;

        $path = null;
        foreach ($finder as $key => $file) {
            if ($rand === $i) {
                $path = $file->getPathname();
            }

            $i++;
        }

        return new File($path);
    }

    protected function getTempDir(): string
    {
        $path = $this->getKernel()->getProjectDir() . '/var/temp';

        if (!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path, 0700);
        }

        return $path;
    }

    protected function getEnvironment(): string
    {
        return $this->getKernel()->getEnvironment();
    }

    protected function getAssetsPath(): string
    {
        return $this->getKernel()->getProjectDir() . '/assets/fixtures';
    }

    protected function getKernel(): Kernel
    {
        return $this->container->get('kernel');
    }

    protected abstract function getYamlPath(): string;
}
