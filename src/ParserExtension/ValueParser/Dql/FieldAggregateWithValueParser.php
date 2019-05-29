<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\ValueParser\Dql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\AggregateNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AggregateWithValueNode;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class FieldAggregateWithValueParser.
 *
 * Subparser that can parse both a raw field or a field wrapped in an aggregate function call with optional value
 */
class FieldAggregateWithValueParser implements SubParserInterface
{
    /**
     * @var SubParserInterface
     */
    private $fieldNameParser;

    /**
     * @var SubParserInterface
     */
    private $valueParser;

    /**
     * FieldAggregateParser constructor.
     *
     * @param SubParserInterface $fieldNameParser
     * @param SubParserInterface $valueParser
     */
    public function __construct(SubParserInterface $fieldNameParser, SubParserInterface $valueParser)
    {
        $this->fieldNameParser = $fieldNameParser;
        $this->valueParser = $valueParser;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(TokenStream $tokenStream)
    {
        if ($tokenStream->lookAhead(1)->test(Token::T_OPEN_PARENTHESIS)) {
            $function = $tokenStream->expect(Token::T_OPERATOR)->getValue();
            $tokenStream->expect(Token::T_OPEN_PARENTHESIS);
            $field = $this->fieldNameParser->parse($tokenStream);

            if ($tokenStream->nextIf(Token::T_COMMA)) {
                $value = $this->valueParser->parse($tokenStream);
            }
            $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

            return new AggregateWithValueNode($function, $field, $value ?? null);
        }

        return $this->fieldNameParser->parse($tokenStream);
    }
}
