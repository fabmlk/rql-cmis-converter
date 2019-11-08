<?php

use Tms\Rql\Factory\CmisqlFactory;
use Xiag\Rql\Parser\Lexer;

require 'dependencies/init.php';

// default lexer supports all RQL rules
$lexer = new Lexer();

$factory = new CmisqlFactory();
$parser = $factory->getParser();

$rql = '(not(like(cmis%3Adocument,tot*))|eq(cmis%3Aname,toto)|(cmis%3Aname<>3&d>=string:4&eq(any(cmis%3Aname),-3)))&u!=5&le(date,2000-01-01T00:00:00Z)&not(or(u=null(),between(i,1,10)))&z=1&contains(coe(l%27hom))&(a==2|b<-3|out(any(c),(2,float:3)))&select(a,sum(b),max(i))&sort(+a,-b)&limit(1,2)&or(intree(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6),infolder(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6))&groupby(type)';
echo $rql . PHP_EOL;

$tokens = $lexer->tokenize($rql);
$builder = $factory->getBuilder(CmisqlFactory::TYPE_PARAMS);

$builder->onVisitExpression(new EchoVisitor());

$tree = $parser->parse($tokens);
$query = $builder->build($tree, 'cmis:document');

echo SqlFormatter::format($query->sql());
var_dump($query->params());