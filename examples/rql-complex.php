<?php

require 'init.php';

// default lexer supports all RQL rules
$lexer = new Xiag\Rql\Parser\Lexer();

$factory = new \Tms\Rql\Cmis\Factory\CmisqlFactory();
$parser = $factory->getParser();

// RQL code
$rql = '(not(like(cmis%3Adocument,tot*))|eq(cmis%3Aname,toto)|(cmis%3Aname<>3&d>=string:4&eq(any(cmis%3Aname),-3)))&u!=5&not(or(u=null(),between(i,1,10)))&z=1&contains(coe(l%27hom))&(a==2|b<-3|out(any(c),(2,float:3)))&select(a,sum(b),max(i))&sort(+a,-b)&limit(1,2)&or(intree(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6),infolder(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6))&groupby(type)';
//$rql = 'like(name,*GU*)&select(name,id,path,modificationDate,creationDate)&(intree(589eb9dd%2D5e4c%2D4f31%2Db88c%2D3d86b3d099d2)|intree(6772dfba%2Dcb3f%2D481f%2D9da4%2De0086c312b7c))&ne(id,ae68d1b7%2D686e%2D46be%2D8cba%2D50c5399b89f0)&ne(id,072f7308%2D1b3d%2D489b%2Daf15%2Dfb1d9a6f7fd9)';
$rql = 'contains(afts(cmis%3Aname,Hello%20from%20postman%20test%20script*))';


// tokenize RQL
$tokens = $lexer->tokenize($rql);

$tree = $parser->parse($tokens);
