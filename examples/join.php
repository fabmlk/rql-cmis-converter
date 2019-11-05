<?php

use Tms\Rql\Factory\CmisqlFactory;
use Tms\Rql\ParserExtension\Node\GroupbyNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\ContainsNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\InFolderNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\InTreeNode;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\SortNode;

require 'dependencies/init.php';

// default lexer supports all RQL rules
$lexer = new Lexer();

// arbitrary example of custom alias resolver
$firstAlias = 'd';
$secondAlias = 'f';
$customAliasResolver = function ($node, $rootAlias) use ($secondAlias) {
    if ($node instanceof SelectNode) {
        $aliases = [];
        foreach ($node->getFields() as $i => $field) {
            if ($field instanceof AbstractNode) {
                $field = $field->getField();
                switch ($field) {
                    case 'i':
                    case 'b':
                        $aliases[$i] = $secondAlias;
                        break;
                    default:
                        $aliases[$i] = $rootAlias;
                }
            } else {
                $aliases[$i] = $rootAlias;
            }
        }

        return $aliases;
    }
    if ($node instanceof SortNode) {
        $aliases = [];
        $i = 0;
        foreach ($node->getFields() as $field => $order) {
            if ('a' === $field) {
                $aliases[$i] = $secondAlias;
            } else {
                $aliases[$i] = $rootAlias;
            }
            $i++;
        }
        return $aliases;
    }
    if ($node instanceof GroupbyNode) {
        $aliases = [];
        foreach ($node->getFields() as $i => $field) {
            if ('type' === $field) {
                $aliases[$i] = $secondAlias;
            } else {
                $aliases[$i] = $rootAlias;
            }
        }
        return $aliases;
    }

    if ($node instanceof ContainsNode) {
        return $rootAlias;
    }
    if ($node instanceof InTreeNode || $node instanceof InFolderNode) {
        return $secondAlias;
    }

    switch ($node->getField()) {
        case 'cmis:document':
        case 'cmis:name':
        case 'z':
        case 'd':
            return $rootAlias;
        default:
            return $secondAlias;
    }
};
$factory = new CmisqlFactory($customAliasResolver);
$parser = $factory->getParser();

$rql = '(not(like(cmis%3Adocument,tot*))|eq(cmis%3Aname,toto)|(cmis%3Aname<>3&d>=string:4&eq(any(cmis%3Aname),-3)))&u!=5&not(or(u=null(),between(i,1,10)))&z=1&contains(coe(l%27hom))&(a==2|b<-3|out(any(c),(2,float:3)))&select(a,sum(b),max(i))&sort(+a,-b)&limit(1,2)&or(intree(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6),infolder(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6))&groupby(type)';
echo $rql . PHP_EOL;

$tokens = $lexer->tokenize($rql);
$builder = $factory->getBuilder(CmisqlFactory::TYPE_SIMPLE);

$builder->onVisitExpression(new EchoVisitor());

$tree = $parser->parse($tokens);
$query = $builder->build($tree, ["cmis:document AS $firstAlias", "cmis:folder AS $secondAlias"], [['inner', "$firstAlias.cmis:objectId = $secondAlias.cmis:objectId"]]);

echo SqlFormatter::format($query->sql());