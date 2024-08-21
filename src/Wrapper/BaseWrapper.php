<?php

declare(strict_types=1);

namespace MewesK\TwigSpreadsheetBundle\Wrapper;

use RuntimeException;
use Twig\Environment;

/**
 * Class BaseWrapper.
 */
abstract class BaseWrapper
{
    protected array $context;

    protected Environment $environment;

    protected array $parameters = [];

    protected array $mappings;

    /**
     * BaseWrapper constructor.
     */
    public function __construct(array $context, Environment $environment)
    {
        $this->context = $context;
        $this->environment = $environment;
        $this->mappings = $this->configureMappings();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getMappings(): array
    {
        return $this->mappings;
    }

    public function setMappings(array $mappings): void
    {
        $this->mappings = $mappings;
    }

    protected function configureMappings(): array
    {
        return [];
    }

    /**
     * Calls the matching mapping callable for each property.
     *
     * @throws RuntimeException
     */
    protected function setProperties(array $properties, ?array $mappings = null, ?string $column = null): void
    {
        if ($mappings === null) {
            $mappings = $this->mappings;
        }

        foreach ($properties as $key => $value) {
            if (!isset($mappings[$key])) {
                throw new RuntimeException(sprintf('Missing mapping for key "%s"', $key));
            }

            if (\is_array($value) && \is_array($mappings[$key])) {
                // recursion
                if (isset($mappings[$key]['__multi'])) {
                    // handle multi target structure (with columns)
                    foreach ($value as $_column => $_value) {
                        $this->setProperties($_value, $mappings[$key], (string) $_column);
                    }
                } else {
                    // handle single target structure
                    $this->setProperties($value, $mappings[$key]);
                }
            } elseif (\is_callable($mappings[$key])) {
                // call single and multi target mapping
                // if column is set it is used to get object from the callback in __multi
                $mappings[$key](
                    $value,
                    $column !== null ? $mappings['__multi']($column) : null
                );
            } else {
                throw new RuntimeException(sprintf('Invalid mapping for key "%s"', $key));
            }
        }
    }
}
