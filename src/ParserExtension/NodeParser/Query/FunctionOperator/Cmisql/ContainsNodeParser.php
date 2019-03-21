<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */

/**
 * http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html#x1-1210004.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\NodeParser\Query\FunctionOperator\Cmisql;

use Tms\Rql\Cmis\ParserExtension\Node\Query\Cmisql\AndContainsNode;
use Tms\Rql\Cmis\ParserExtension\Node\Query\Cmisql\OrContainsNode;
use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\AftsNode;
use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\CoeNode;
use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\ColNode;
use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\NceNode;
use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\NclNode;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\OrNode;
use Xiag\Rql\Parser\NodeParser\Query\GroupNodeParser;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

/**
 * Class ContainsNodeParser.
 *
 * Parser for contains() function (http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html#x1-1210004).
 * Note: this does not support mixing of AND and OR (but allowed in the spec)
 */
class ContainsNodeParser implements NodeParserInterface
{
    /**
     * @var GroupNodeParser
     */
    protected $groupParser;

    /**
     * @param GroupNodeParser $groupParser
     */
    public function __construct(GroupNodeParser $groupParser)
    {
        $this->groupParser = $groupParser;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(TokenStream $tokenStream): AbstractNode
    {
        $tokenStream->expect(Token::T_OPERATOR, 'contains');
        // the trick here is to use a group parser directly as the parsing logic inside
        // contains() is exactly the same as of a group parser (except mixing of AND and OR should be allowed)
        $node = $this->groupParser->parse($tokenStream);

        switch (true) {
            case $node instanceof AndNode:
                return new AndContainsNode($node->getQueries());
            case $node instanceof OrNode:
                return new OrContainsNode($node->getQueries());
            case $node instanceof CoeNode:
            case $node instanceof NceNode:
            case $node instanceof ColNode:
            case $node instanceof NclNode:
            case $node instanceof AftsNode:
                // We could return OrContainsNode as well, this will just act as a wrapper
                return new AndContainsNode([$node]);
            default:
                throw new SyntaxErrorException(
                    sprintf(
                        'Unexpected instance of %s node: expected amongst %s',
                        get_class($node),
                        implode(
                            ', ',
                            [
                                AndNode::class,
                                OrNode::class,
                                CoeNode::class,
                                NceNode::class,
                                ColNode::class,
                                NclNode::class,
                                AftsNode::class,
                            ]
                        )
                    )
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenStream $tokenStream): bool
    {
        return $tokenStream->test(Token::T_OPERATOR, 'contains');
    }
}
