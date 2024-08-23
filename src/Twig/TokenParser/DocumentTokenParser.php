<?php

declare(strict_types=1);

namespace MewesK\TwigSpreadsheetBundle\Twig\TokenParser;

use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use MewesK\TwigSpreadsheetBundle\Twig\Node\DocumentNode;
use Twig\Token;

class DocumentTokenParser extends BaseTokenParser
{
    public function configureParameters(Token $token): array
    {
        return [
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
        return new DocumentNode($nodes, $this->getAttributes(), $lineNo, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'xlsdocument';
    }
}
