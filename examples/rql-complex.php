<?php

require 'init.php';

// default lexer supports all RQL rules
$lexer = new Xiag\Rql\Parser\Lexer();

$factory = new \Tms\Rql\Factory\CmisqlFactory();
$parser = $factory->getParser();

// RQL code
$rql = '(not(like(cmis%3Adocument,tot*))|eq(cmis%3Aname,toto)|(cmis%3Aname<>3&d>=string:4&eq(any(cmis%3Aname),-3)))&u!=5&not(or(u=null(),between(i,1,10)))&z=1&contains(coe(l%27hom))&(a==2|b<-3|out(any(c),(2,float:3)))&select(a,sum(b),max(i))&sort(+a,-b)&limit(1,2)&intree(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6)&groupby(type)';

// tokenize RQL
$tokens = $lexer->tokenize($rql);

$tree = $parser->parse($tokens);
