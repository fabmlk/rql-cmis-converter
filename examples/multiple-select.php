<?php

use Tms\Rql\Factory\SqlFactory;
use Xiag\Rql\Parser\Lexer;

require 'dependencies/init.php';

// default lexer supports all RQL rules
$lexer = new Lexer();

$factory = new SqlFactory();
$parser = $factory->getParser();

// RQL code: only the last select should be kept
$rql = 'select(a)&eq(a,toto)&sort(+a)&select(b)&select(c)';
echo $rql . PHP_EOL;

$tokens = $lexer->tokenize($rql);
$builder = $factory->getBuilder(SqlFactory::TYPE_SIMPLE);

$builder->onVisitExpression(new EchoVisitor());

$tree = $parser->parse($tokens);
$query = $builder->build($tree, 'my_table');

echo SqlFormatter::format($query->sql());