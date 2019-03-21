<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\NodeParser;

use Tms\Rql\Cmis\ParserExtension\Node\GroupbyNode;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class GroupbyNodeParser.
 *
 * {@link https://github.com/xiag-ag/rql-parser/blob/master/examples/03-new-top-operator.php}
 */
class GroupbyNodeParser implements NodeParserInterface
{
    /**
     * @param TokenStream $tokenStream
     *
     * @return GroupbyNode
     */
    public function parse(TokenStream $tokenStream): GroupbyNode
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'groupby');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $fields[] = $tokenStream->expect(Token::T_STRING)->getValue();
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new GroupbyNode($fields);
    }

    /**
     * @param TokenStream $tokenStream
     *
     * @return bool
     */
    public function supports(TokenStream $tokenStream): bool
    {
        return $tokenStream->test(Token::T_OPERATOR, 'groupby');
    }
}
