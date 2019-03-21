<?php

require 'init.php';

// default lexer supports all RQL rules
$lexer = new Xiag\Rql\Parser\Lexer();

$factory = new \Tms\Rql\Cmis\Factory\CmisqlFactory();
$parser = $factory->getParser();

// RQL code: only the last select should be kept
$rql = 'select(a)&eq(a,toto)&sort(+a)&select(b)&select(c)';

// tokenize RQL
$tokens = $lexer->tokenize($rql);

$tree = $parser->parse($tokens);