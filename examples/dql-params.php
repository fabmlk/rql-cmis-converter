<?php

use Tms\Rql\Factory\DqlFactory;
use Xiag\Rql\Parser\Lexer;

require 'dependencies/init.php';

// default lexer supports all RQL rules
$lexer = new Lexer();

$factory = new DqlFactory('o');
$parser = $factory->getParser();

$rql = '(not(like(document,tot*))|eq(identity(group),toto)|(name<>3&d>=string:4&eq(name,-3)))&u!=5&not(or(u=null(),between(i,1,10)))&z=1&(a==2|b<-3|out(c,(2,float:3)))&select(a,sum(b),max(i),identity(a,a))&sort(+a,-b)&limit(1,2)&groupby(type)';
echo $rql . PHP_EOL;

$tokens = $lexer->tokenize($rql);
$builder = $factory->getBuilder(DqlFactory::TYPE_PARAMS);

$builder->onVisitExpression(new EchoVisitor());

$tree = $parser->parse($tokens);
$query = $builder->build($tree, 'My\Entity');

echo SqlFormatter::format($query->sql());
var_dump($query->params());