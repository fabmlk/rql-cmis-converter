<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\ValueParser\Cmisql;

use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class FieldParser.
 *
 * Field parser that accepts also syntax 'string:string'
 */
class FieldParser implements SubParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $soFar = '';
        if ($typeToken = $tokenStream->nextIf(Token::T_TYPE)) {
            $soFar .= $typeToken->getValue();
            $soFar .= $tokenStream->expect(Token::T_COLON)->getValue();
        }

        return $soFar.$tokenStream->expect(Token::T_STRING)->getValue();
    }
}
