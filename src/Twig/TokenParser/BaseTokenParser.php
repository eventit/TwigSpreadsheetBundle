<?php

declare(strict_types=1);

namespace MewesK\TwigSpreadsheetBundle\Twig\TokenParser;

use Exception;
use InvalidArgumentException;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use function count;

abstract class BaseTokenParser extends AbstractTokenParser
{
    /**
     * @var int
     */
    public const PARAMETER_TYPE_ARRAY = 0;

    /**
     * @var int
     */
    public const PARAMETER_TYPE_VALUE = 1;

    /**
     * BaseTokenParser constructor.
     *
     * @param array $attributes optional attributes for the corresponding node
     */
    public function __construct(private array $attributes = [])
    {
    }

    public function configureParameters(Token $token): array
    {
        return [];
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Create a concrete node.
     */
    abstract public function createNode(array $nodes = [], int $lineNo = 0): Node;

    public function hasBody(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function parse(Token $token): Node
    {
        // parse parameters
        $nodes = $this->parseParameters($this->configureParameters($token));

        // parse body
        if ($this->hasBody()) {
            $nodes['body'] = $this->parseBody();
        }

        return $this->createNode($nodes, $token->getLine());
    }

    /**
     * @return AbstractExpression[]
     * @throws InvalidArgumentException
     * @throws SyntaxError
     * @throws Exception
     */
    private function parseParameters(array $parameterConfiguration = []): array
    {
        // parse expressions
        $expressions = [];
        while (!$this->parser->getStream()->test(Token::BLOCK_END_TYPE)) {
            $expressions[] = $this->parser->getExpressionParser()->parseExpression();
        }

        // end of expressions
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        // map expressions to parameters
        $parameters = [];
        foreach ($parameterConfiguration as $parameterName => $parameterOptions) {
            // try mapping expression
            $expression = reset($expressions);
            if ($expression !== false) {
                $valid = match ($parameterOptions['type']) {
                    self::PARAMETER_TYPE_ARRAY => $expression instanceof ArrayExpression,
                    self::PARAMETER_TYPE_VALUE => !($expression instanceof ArrayExpression),
                    default => throw new InvalidArgumentException('Invalid parameter type'),
                };

                if ($valid) {
                    // set expression as parameter and remove it from expressions list
                    $parameters[$parameterName] = array_shift($expressions);
                    continue;
                }
            }

            // set default as parameter otherwise or throw exception if default is false
            if ($parameterOptions['default'] === false) {
                throw new SyntaxError('A required parameter is missing');
            }
            $parameters[$parameterName] = $parameterOptions['default'];
        }

        if (count($expressions) > 0) {
            throw new SyntaxError('Too many parameters');
        }

        return $parameters;
    }

    /**
     * @return Node
     * @throws SyntaxError
     */
    private function parseBody(): Node
    {
        // parse till matching end tag is found
        $body = $this->parser->subparse(fn(Token $token) => $token->test('end' . $this->getTag()), true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return $body;
    }
}
