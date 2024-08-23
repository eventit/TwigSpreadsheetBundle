<?php

declare(strict_types=1);

namespace MewesK\TwigSpreadsheetBundle\Twig\NodeVisitor;

use Exception;
use MewesK\TwigSpreadsheetBundle\Twig\Node\BaseNode;
use MewesK\TwigSpreadsheetBundle\Twig\Node\DocumentNode;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\NodeVisitor\AbstractNodeVisitor;

class SyntaxCheckNodeVisitor extends AbstractNodeVisitor
{
    protected array $path = [];

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws SyntaxError
     */
    protected function doEnterNode(Node $node, Environment $env): Node
    {
        try {
            if ($node instanceof BaseNode) {
                $this->checkAllowedParents($node);
            } else {
                $this->checkAllowedChildren($node);
            }
        } catch (SyntaxError $e) {
            // reset path since throwing an error prevents doLeaveNode to be called
            $this->path = [];
            throw $e;
        }

        $this->path[] = $node::class;

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env): ?Node
    {
        array_pop($this->path);

        return $node;
    }

    /**
     * @throws SyntaxError
     * @throws Exception
     */
    private function checkAllowedChildren(Node $node): void
    {
        $hasDocumentNode = false;
        $hasTextNode = false;

        /**
         * @var Node $currentNode
         */
        foreach ($node->getIterator() as $currentNode) {
            if ($currentNode instanceof TextNode) {
                if ($hasDocumentNode) {
                    throw new SyntaxError(sprintf('Node "%s" is not allowed after Node "%s".', TextNode::class, DocumentNode::class));
                }
                $hasTextNode = true;
            } elseif ($currentNode instanceof DocumentNode) {
                if ($hasTextNode) {
                    throw new SyntaxError(sprintf('Node "%s" is not allowed before Node "%s".', TextNode::class, DocumentNode::class));
                }
                $hasDocumentNode = true;
            }
        }
    }

    /**
     * @throws SyntaxError
     */
    private function checkAllowedParents(BaseNode $node): void
    {
        $parentName = null;

        // find first parent from this bundle
        foreach (array_reverse($this->path) as $className) {
            if (str_starts_with($className, 'MewesK\\TwigSpreadsheetBundle\\Twig\\Node\\')) {
                $parentName = $className;
                break;
            }
        }

        // allow no parents (e.g. macros, includes)
        if ($parentName === null) {
            return;
        }

        // check if parent is allowed
        if (in_array($parentName, $node->getAllowedParents(), true)) {
            return;
        }

        throw new SyntaxError(sprintf('Node "%s" is not allowed inside of Node "%s".', $node::class, $parentName));
    }
}
