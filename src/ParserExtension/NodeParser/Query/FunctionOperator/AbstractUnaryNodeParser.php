<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\NodeParser\Query\FunctionOperator;

use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class AbstractUnaryNodeParser.
 */
abstract class AbstractUnaryNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    private $valueParser;

    /**
     * BetweenNodeParser constructor.
     *
     * @param SubParserInterface $valueParser
     */
    public function __construct(SubParserInterface $valueParser)
    {
        $this->valueParser = $valueParser;
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
        $value = $this->valueParser->parse($tokenStream);
        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->getNode($value);
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
}
