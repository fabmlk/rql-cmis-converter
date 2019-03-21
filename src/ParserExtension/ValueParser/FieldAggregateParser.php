<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\ValueParser;

use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\AggregateNode;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class FieldAggregateParser.
 *
 * Subparser that can parse both a raw field or a field wrapped in an aggregate function call
 */
class FieldAggregateParser implements SubParserInterface
{
    /**
     * @var SubParserInterface
     */
    private $fieldNameParser;

    /**
     * FieldAggregateParser constructor.
     *
     * @param SubParserInterface $fieldNameParser
     */
    public function __construct(SubParserInterface $fieldNameParser)
    {
        $this->fieldNameParser = $fieldNameParser;
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
            $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

            return new AggregateNode($function, $field);
        }

        return $this->fieldNameParser->parse($tokenStream);
    }
}
