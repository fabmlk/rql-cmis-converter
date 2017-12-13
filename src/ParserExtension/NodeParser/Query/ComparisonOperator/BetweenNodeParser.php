<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\ComparisonOperator;

use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\BetweenNode;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class BetweenNodeParser.
 *
 * {@link https://github.com/xiag-ag/rql-parser/blob/master/examples/02-new-query-operator.php}
 */
class BetweenNodeParser implements NodeParserInterface
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
     * @param TokenStream $tokenStream
     *
     * @return BetweenNode
     */
    public function parse(TokenStream $tokenStream): BetweenNode
    {
        $tokenStream->expect(Token::T_OPERATOR, 'between');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);
        $from = $this->valueParser->parse($tokenStream);
        $tokenStream->expect(Token::T_COMMA);
        $to = $this->valueParser->parse($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new BetweenNode($field, $from, $to);
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function supports(TokenStream $tokenStream): bool
    {
        return $tokenStream->test(Token::T_OPERATOR, 'between');
    }
}
