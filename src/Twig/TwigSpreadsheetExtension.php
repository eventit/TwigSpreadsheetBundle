<?php

namespace MewesK\TwigSpreadsheetBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use InvalidArgumentException;
use Twig\Error\RuntimeError;
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

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
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
    public function getTokenParsers()
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
    public function getNodeVisitors()
    {
        return [
            new MacroContextNodeVisitor(),
            new SyntaxCheckNodeVisitor(),
        ];
    }

    /**
     *
     * @throws RuntimeError
     *
     * @return array
     */
    public function mergeStyles(array $style1, array $style2): array
    {
        if (!\is_array($style1) || !\is_array($style2)) {
            throw new RuntimeError('The xlsmergestyles function only works with arrays.');
        }
        return Arrays::mergeRecursive($style1, $style2);
    }

    /**
     *
     * @throws RuntimeError
     * @return int|null
     */
    public function getCurrentColumn(array $context) {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentColumn();
    }

    /**
     *
     * @throws RuntimeError
     * @return int|null
     */
    public function getCurrentRow(array $context) {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentRow();
    }
}
