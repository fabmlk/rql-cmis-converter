RQL CMIS CONVERTER
=====

Parseur RQL pouvant générer des requêtes CMIS QL.

Cette librairie peut se décomposer en 2 parties:
  - extension du parseur RQL proposé par [xiag-ag/rql-parser](https://github.com/xiag-ag/rql-parser) afin d'y ajouter des fonctionalités utiles à CMIS-QL
  - génération d'une requête CMIS-QL à partir de l'arbre composé par le parseur

Principe
--------

RQL (Resource Query Language) est un langage de requête proposé par [Persevere](http://www.persvr.org) destiné à être utilisé directement en query string d'URIs pour opérer sur des structures de données de style "objet".

Une des utilisations possibles est donc d'effectuer des requêtes SQL ou CMIS QL de type `SELECT`.

Voir section [resources](#resources) pour une présentation plus détaillée du langage RQL.


Usage basique
-----------

```php
<?php
require 'vendor/autoload.php';

// default lexer supports all RQL rules
$lexer = new Xiag\Rql\Parser\Lexer();

$factory = new Tms\Rql\Factory\CmisqlFactory();
$parser = $factory->getParser();

// RQL code
$rql = '(not(like(cmis%3Adocument,tot*))|eq(cmis%3Aname,toto)|(cmis%3Aname<>3&d>=string:4&eq(any(cmis%3Aname),-3)))&u!=5&not(or(u=null(),between(i,1,10)))&z=1&contains(coe(l%27hom))&(a==2|b<-3|out(any(c),(2,float:3)))&select(a,sum(b),max(i))&sort(+a,-b)&limit(1,2)&intree(e201839d%2Dd3f7%2D4fa1%2Dbbe4%2D7253163ed7a6)&groupby(type)';

// tokenize RQL
$tokens = $lexer->tokenize($rql);
// parse
$tree = $parser->parse($tokens);

// generate CMIS QL
$builder = $factory->getBuilder(Tms\Rql\Factory\CmisqlFactory::TYPE_PARAMS);
$query = $builder->build($tree, 'cmis:document');
echo $query->sql() . PHP_EOL;
var_dump($query->params());
```

Voir aussi [rql-command library](https://github.com/xiag-ag/rql-command) pour debugger lexing et parsing du RQL.



Fonctionalités RQL supportées
-------------------------

## Types

- types dans la catégorie "scalaire"

    Type | Examples
    ---|---
    string | abc, abc%20def
    integer | 1, +1, -1
    float | 1., +1., -1., 0.1, +0.1, -0.1, 0.1e5, +0.1e+5, -0.1e-5, .1, +.1, -.1, .1e5, +.1e+5, -.1e-5
    boolean | true(), false()
    null | null()
    empty | empty()

- types dans leur propre catégorie

    Type | Examples
    ---|---
    date | 2015-06-02T20:00:00Z
    glob | abc*, ?abc, abc*def?
    array | (1,-2,abc,true(),null(),*def)
    
Il est également possible de forcer un type en cas d'ambiguité via type casting:

Type | Examples
---|---
string | string:1, string:true()
boolean | boolean:0, boolean:null()
integer | integer:a, integer:0.1
float | float:1, float:-1

Toues les caractères non-alphanumériques présents dans un string doivent être par le signe pourcent (%) suivi de deux chiffres hexadécimaux:

```
eq(string,2015%2D05%2D30T15%3A10%3A00Z)
in(string,(%2B1%2E5,%2D1%2E5))
in(string,(null%28%29,empty%28%29,true%28%29,false%28%29))
```

A contrario, les dates, nombres et fonctions de constantes ne doivent pas être encodées:

```
eq(date,2015-05-30T15:10:00Z)
in(number,(+1.5,-1.5))
in(const,(null(),empty(),true(),false()))
```

Implémentations possibles:
```php
// function d'encodage en PHP
function encodeString($value)
{
    return strtr(rawurlencode($value), [
        '-' => '%2D',
        '_' => '%5F',
        '.' => '%2E',
        '~' => '%7E',
    ]);
}
```

```js
// fonction d'encodage en Javascript
function encodeString(value) {
    return encodeURIComponent(value).replace(/[\-_\.~!\\'\*\(\)]/g, function (char) {
        return '%' + char.charCodeAt(0).toString(16).toUpperCase();
    });
}
```



  
## Opérateurs  
- opérateurs scalaires
 
    RQL | CMIS QL | Abréviation de | Typage
    ---|---|---|---
    eq(a,b) | a = b | <u>EQ</u>ual | (string, scalaire)
    ne(a,b) | a <> b | <u>N</u>ot <u>E</u>qual | (string, scalaire)
    lt(a,b) | a < b | <u>L</u>ess <u>T</u>han | (string, scalaire)
    gt(a,b) | a > b | <u>G</u>reater <u>T</u>han | (string, scalaire)
    le(a,b) | a \<= b | <u>L</u>ess than or <u>E</u>qual | (string, scalaire)
    ge(a,b) | a >= b | <u>G</u>reater than or <u>E</u>qual | (string, scalaire)
    like(a,b) | a LIKE b | | (string, glob)
    between(a,b,c) | a BETWEEN b AND c | | (string, scalaire, scalaire)
    eq(any(a),b) | b = ANY a | | (string, scalaire)
    
- opérateurs scalaires pour CONTAINS()

    RQL | CMIS QL | Abréviation de | Typage
    ---|---|---|---
    contains(coe(a)) | CONTAINS('"a"') | <u>CO</u>ntains <u>E</u>xactly | scalaire
    contains(nce(a)) | CONTAINS('-"a"') | <u>N</u>ot <u>C</u>ontains <u>E</u>xactly | scalaire
    contains(col(a)) | CONTAINS('a') | <u>CO</u>ntains <u>L</u>ike | glob
    contains(ncl(a)) | CONTAINS('-a') | <u>N</u>ot <u>C</u>ontains <u>L</u>ike | glob
    
- opérateurs de tableaux
 
     RQL | CMIS QL | Typage
    ---|---|---
    in(a,(b,c)) | a IN (b, c) | (string, array)
    in(any(a),(b,c)) | ANY a IN (b, c) | (string, array)
    out(a,(b,c)) | a NOT IN (b, c) | (string, array)
    out(any(a),(b,c)) | ANY a NOT IN (b, c) | (string, array)
    
- opérateurs logiques
 
      RQL | CMIS QL
    ---|---|---
    and(eq(a,b),ne(c,d)) | (a = b AND c <> d)
    or(eq(a,b),ne(c,d)) | (a = b OR c <> d)
    eq(a,b)&ne(b,c) | (a = b AND b <> d)
    (eq(a,b)&#124;ne(b,c)) | (a = b OR c <> d)
    not(eq(a,b)) | NOT (a = b)
    
    **Remarque:**
    - l'opérateur logique `|` peut uniquement se situer à l'intérieur d'un groupe. Ceci est exprimé ci-dessus par la présence des `()`. En effet, comme RQL doit être compatible avec les règles d'un query string d'URI, seule l'opérateur logique `&` est toléré en "top-level".

- opérateurs de fonctions

    RQL | CMIS QL | Typage
    ---|---|---
    select(a,b) | SELECT(a,b) | (string, string, ...)
    sort(+a,-b) | ORDER BY a ASC, b DESC | (string, string)
    intree(a) | IN_TREE('a') | scalaire
    contains(col(a)) | CONTAINS('a') | 

    **Remarques:**
    - Bien qu'obligatoire dans la requête CMIS QL finale, l'opérateur RQL `select()` est optionnel. Si absent, son equivalent CMIS QL généré sera `SELECT *`
    - Si plusieurs `select()` sont présents, seul le dernier est pris en compte
    - La clause CMIS QL `FROM <table>` n'est pas générée à partir d'un opérateur RQL. La table concernée est transmise en 2e argument de la méthode `QueryBuilderInterface::build()`
    - Le standard CMIS QL ne dipose pas de clauses `LIMIT`, `GROUP BY` ou `DISTINCT` ainsi que les fonctions d'aggrégations usuelles définies par SQL-92

Limitations
-----------
* Les opérations de jointures de type `JOIN` ne sont pas supportées

Exemples
--------

Description | RQL | CMIS QL
---|---|---
Obtenir tous les éléments dont le nom commence par "Decl"| like(cmis%3Aname,Decl*) | SELECT * FROM \<table\> WHERE cmis:name LIKE 'Decl%'
Obtenir les noms des éléments dont le nom commence par "Op" hormis ceux nommés "Opération" | select(cmis%3Aname)&like(cmis%3Aname,Op*)&ne(cmis%3Aname,Op%C3%A9ration) | SELECT cmis:name FROM \<table\> WHERE (cmis:name LIKE 'Op%' AND cmis:name <> 'Opération') 
Obtenir les 10 éléments les plus anciens | sort(+cmis%3AcreationDate)&limit(10) | SELECT * FROM \<table\> ORDER BY cmis:creationDate ASC LIMIT 10
Obtenir tous les éléments contenant le texte exact "date de" mais pas "date de validation" | contains(coe(date%20de)&nce(date%20de%20validation)) | SELECT * FROM \<table\> WHERE CONTAINS('"date de" AND -"date de validation"')
Obtenir tous les éléments situés sous la racine d'id "123" | intree(string:123) | SELECT * IN_TREE('123')






<a name="resources"></a>Resources
---------
 * [Règles RQL](https://github.com/persvr/rql)
 * [Article RQL](https://www.sitepen.com/blog/2010/11/02/resource-query-language-a-query-language-for-the-web-nosql/)