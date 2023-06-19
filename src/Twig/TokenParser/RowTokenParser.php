<?php

namespace MewesK\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use MewesK\TwigSpreadsheetBundle\Twig\Node\RowNode;

/**
 * Class RowTokenParser.
 */
class RowTokenParser extends BaseTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function configureParameters(\Twig\Token $token): array
    {
        return [
            'index' => [
                'type' => self::PARAMETER_TYPE_VALUE,
                'default' => new ConstantExpression(null, $token->getLine()),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createNode(array $nodes = [], int $lineNo = 0): Node
    {
        return new RowNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'xlsrow';
    }
}
