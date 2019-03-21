<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\NodeParser;

use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * select(field1,count(field2),sum(field3),...).
 *
 * {@link https://github.com/xiag-ag/rql-parser/blob/master/examples/03-new-top-operator.php}
 */
class SelectNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    private $fieldAggregateParser;

    /**
     * SelectNodeParser constructor.
     *
     * @param SubParserInterface $fieldAggregateParser
     */
    public function __construct(SubParserInterface $fieldAggregateParser)
    {
        $this->fieldAggregateParser = $fieldAggregateParser;
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return SelectNode
     */
    public function parse(TokenStream $tokenStream): SelectNode
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'select');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $fields[] = $this->fieldAggregateParser->parse($tokenStream);

            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SelectNode($fields);
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function supports(TokenStream $tokenStream): bool
    {
        return $tokenStream->test(Token::T_OPERATOR, 'select');
    }
}
