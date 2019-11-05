<?php

use Tms\Rql\Factory\DqlFactory;
use Tms\Rql\ParserExtension\Node\GroupbyNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AtDepthNode;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\SortNode;

require 'dependencies/init.php';

// default lexer supports all RQL rules
$lexer = new Lexer();
$factory = new DqlFactory();
$parser = $factory->getParser();

$rql = '(not(like(document,tot*))|atdepth(1234,4)|eq(identity(group),toto)|(name<>3&d>=string:4&eq(name,-3)))&u!=5&not(or(u=null(),between(i,1,10)))&z=1&(a==2|b<-3|out(c,(2,float:3)))&select(a,sum(b),max(i),identity(a,a))&sort(+a,-b)&limit(1,2)&groupby(type)';
echo $rql . PHP_EOL;

$tokens = $lexer->tokenize($rql);
$builder = $factory->getBuilder(DqlFactory::TYPE_SIMPLE);

$builder->onVisitExpression(new EchoVisitor());

$tree = $parser->parse($tokens);
$query = $builder->build($tree, 'My\Entity');

echo SqlFormatter::format($query->sql());