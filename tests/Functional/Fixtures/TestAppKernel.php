<?php

namespace MewesK\TwigSpreadsheetBundle\Tests\Functional\Fixtures;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use MewesK\TwigSpreadsheetBundle\MewesKTwigSpreadsheetBundle;
use MewesK\TwigSpreadsheetBundle\Tests\Functional\Fixtures\TestBundle\TestBundle;
use Exception;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TestAppKernel.
 */
class TestAppKernel extends Kernel
{
    private ?string $cacheDir = null;

    private ?string $logDir = null;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SensioFrameworkExtraBundle(),
            new MewesKTwigSpreadsheetBundle(),
            new TestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', $this->rootDir, $this->getEnvironment()));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function setCacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->logDir;
    }

    public function setLogDir(string $logDir)
    {
        $this->logDir = $logDir;
    }
}
