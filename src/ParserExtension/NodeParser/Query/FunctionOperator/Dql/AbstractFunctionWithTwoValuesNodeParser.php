<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\Dql;

use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Exemple: intree(1234,4)
 */
abstract class AbstractFunctionWithTwoValuesNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    private $leftValueParser;

    /**
     * @var SubParserInterface
     */
    private $rightValueParser;

    /**
     * AbstractFunctionWithTwoValuesNodeParser constructor.
     *
     * @param SubParserInterface $leftValueParser
     * @param SubParserInterface $rightValueParser
     */
    public function __construct(SubParserInterface $leftValueParser, SubParserInterface $rightValueParser)
    {
        $this->leftValueParser = $leftValueParser;
        $this->rightValueParser = $rightValueParser;
    }

    /**
     * {@inheritdoc}
     *
     * @return AbstractNode
     */
    public function parse(TokenStream $tokenStream): AbstractNode
    {
        $tokenStream->expect(Token::T_OPERATOR, $this->getNodeName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);
        $left = $this->leftValueParser->parse($tokenStream);
        $tokenStream->expect(Token::T_COMMA);
        $right = $this->rightValueParser->parse($tokenStream);
        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->getNode($left, $right);
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function supports(TokenStream $tokenStream): bool
    {
        return $tokenStream->test(Token::T_OPERATOR, $this->getNodeName());
    }

    /**
     * @return string
     */
    abstract public function getNodeName(): string;

    /**
     * @param $left
     * @param $right
     *
     * @return AbstractNode
     */
    abstract public function getNode($left, $right): AbstractNode;
}
