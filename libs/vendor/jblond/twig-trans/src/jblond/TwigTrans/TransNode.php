<?php

namespace jblond\TwigTrans;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\TempNameExpression;
use Twig\Node\PrintNode;

/**
 * Class TransNode
 * @package jblond\TwigTrans
 */
#[YieldReady]
class TransNode extends Nodes
{
    /**
     * TransNode constructor.
     * @param Nodes|\Twig\Node\TextNode|NameExpression $body
     * @param Nodes|Twig\Node\TextNode|null $plural
     * @param AbstractExpression|null $count
     * @param Nodes|\Twig\Node\TextNode|null $notes
     * @param int $lineNo
     */
    public function __construct(
        $body,
        $plural = null,
        ?AbstractExpression $count = null,
        $notes = null,
        int $lineNo = 0
    ) {
        $nodes = ['body' => $body];
        if (null !== $count) {
            $nodes['count'] = $count;
        }
        if (null !== $plural) {
            $nodes['plural'] = $plural;
        }
        if (null !== $notes) {
            $nodes['notes'] = $notes;
        }

        parent::__construct($nodes, $lineNo);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);
        $msg1 = new Nodes();

        [$msg, $vars] = $this->compileString($this->getNode('body'));

        if ($this->hasNode('plural')) {
            [$msg1, $vars1] = $this->compileString($this->getNode('plural'));

            $vars = array_merge($vars, $vars1);
        }

        $function = $this->getTransFunction($this->hasNode('plural'));

        if ($this->hasNode('notes')) {
            $message = trim($this->getNode('notes')->getAttribute('data'));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(["\n", "\r"], ' ', $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler
                ->write('yield strtr(' . $function . '(')
                ->subcompile($msg)
            ;

            if ($this->hasNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->hasNode('count') ? $this->getNode('count') : new Nodes())
                    ->raw(')')
                ;
            }

            $compiler->raw('), array(');

            foreach ($vars as $var) {
                if ('count' === $var->getAttribute('name')) {
                    $compiler
                        ->string('%count%')
                        ->raw(' => abs(')
                        ->subcompile($this->hasNode('count') ? $this->getNode('count') : new Nodes())
                        ->raw('), ')
                    ;
                } else {
                    $compiler
                        ->string('%' . $var->getAttribute('name') . '%')
                        ->raw(' => ')
                        ->subcompile($var)
                        ->raw(', ')
                    ;
                }
            }

            $compiler->raw("));\n");
        } else {
            $compiler
                ->write('yield ' . $function . '(')
                ->subcompile($msg)
            ;

            if ($this->hasNode('plural')) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->hasNode('count') ? $this->getNode('count') : new Nodes())
                    ->raw(')')
                ;
            }

            $compiler->raw(");\n");
        }
    }

    /**
     * @param Nodes|\Twig\Node\TextNode $body A Twig_Node instance
     *
     * @return array
     */
    protected function compileString($body): array
    {
        if (
            $body instanceof NameExpression ||
            $body instanceof ConstantExpression ||
            $body instanceof TempNameExpression
        ) {
            return [$body, []];
        }

        $vars = [];
        if (count($body)) {
            $msg = '';

            /** @var Nodes $node */
            foreach ($body as $node) {
                if (get_class($node) === 'Nodes' && $node->getNode('0') instanceof TempNameExpression) {
                    $node = $node->getNode('1');
                }

                if ($node instanceof PrintNode) {
                    $n = $node->getNode('expr');
                    while ($n instanceof FilterExpression) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%%%s%%', $n->getAttribute('name'));
                    $vars[] = new NameExpression($n->getAttribute('name'), $n->getTemplateLine());
                } else {
                    $msg .= $node->getAttribute('data');
                }
            }
        } else {
            $msg = $body->getAttribute('data');
        }

        return [new Nodes([new ConstantExpression(trim($msg), $body->getTemplateLine())]), $vars];
    }

    /**
     * @param bool $plural Return plural or singular function to use
     *
     * @return string
     */
    protected function getTransFunction(bool $plural): string
    {
        return $plural ? 'ngettext' : 'gettext';
    }
}
