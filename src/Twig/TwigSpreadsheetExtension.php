<?php

declare(strict_types=1);

namespace MewesK\TwigSpreadsheetBundle\Twig;

use InvalidArgumentException;
use MewesK\TwigSpreadsheetBundle\Helper\Arrays;
use MewesK\TwigSpreadsheetBundle\Twig\NodeVisitor\MacroContextNodeVisitor;
use MewesK\TwigSpreadsheetBundle\Twig\NodeVisitor\SyntaxCheckNodeVisitor;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\AlignmentTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\CellTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\DocumentTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\DrawingTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\HeaderFooterTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\RowTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\SheetTokenParser;
use MewesK\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;
use MewesK\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function is_array;


/**
 * Class TwigSpreadsheetExtension.
 */
class TwigSpreadsheetExtension extends AbstractExtension
{
    /**
     * TwigSpreadsheetExtension constructor.
     */
    public function __construct(private array $attributes = [])
    {
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('xlsmergestyles', [$this, 'mergeStyles']),
            new TwigFunction('xlscellindex', [$this, 'getCurrentColumn'], ['needs_context' => true]),
            new TwigFunction('xlsrowindex', [$this, 'getCurrentRow'], ['needs_context' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function getTokenParsers(): array
    {
        return [
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_CENTER),
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_LEFT),
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_RIGHT),
            new CellTokenParser(),
            new DocumentTokenParser($this->attributes),
            new DrawingTokenParser(),
            new HeaderFooterTokenParser([], HeaderFooterWrapper::BASETYPE_FOOTER),
            new HeaderFooterTokenParser([], HeaderFooterWrapper::BASETYPE_HEADER),
            new RowTokenParser(),
            new SheetTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors(): array
    {
        return [
            new MacroContextNodeVisitor(),
            new SyntaxCheckNodeVisitor(),
        ];
    }

    /**
     * @throws RuntimeError
     */
    public function mergeStyles(array $style1, array $style2): array
    {
        if (!is_array($style1) || !is_array($style2)) {
            throw new RuntimeError('The xlsmergestyles function only works with arrays.');
        }
        return Arrays::mergeRecursive($style1, $style2);
    }

    /**
     * @throws RuntimeError
     */
    public function getCurrentColumn(array $context): ?int
    {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentColumn();
    }

    /**
     * @throws RuntimeError
     */
    public function getCurrentRow(array $context): ?int
    {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentRow();
    }
}
