<?php

namespace MewesK\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use MewesK\TwigSpreadsheetBundle\Twig\Node\DrawingNode;

/**
 * Class DrawingTokenParser.
 */
class DrawingTokenParser extends BaseTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function configureParameters(\Twig\Token $token): array
    {
        return [
            'path' => [
                'type' => self::PARAMETER_TYPE_VALUE,
                'default' => false,
            ],
            'properties' => [
                'type' => self::PARAMETER_TYPE_ARRAY,
                'default' => new ArrayExpression([], $token->getLine()),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createNode(array $nodes = [], int $lineNo = 0): Node
    {
        return new DrawingNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'xlsdrawing';
    }

    /**
     * {@inheritdoc}
     */
    public function hasBody(): bool
    {
        return false;
    }
}
