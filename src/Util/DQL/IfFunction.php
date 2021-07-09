<?php

namespace App\Util\DQL;

use Doctrine\ORM\Query\AST\ConditionalPrimary;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class IfFunction extends FunctionNode
{
    private ConditionalPrimary $condition;
    
    private array $values = [];

    /**
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        
        $this->condition = $parser->ConditionalExpression();

        $parser->match(Lexer::T_COMMA);

        if ($parser->getLexer()->isNextToken(Lexer::T_NULL)) {
            $parser->match(Lexer::T_NULL);
            $this->values[] = null;
        } else {
            $this->values[] = $parser->ArithmeticExpression();
        }

        $parser->match(Lexer::T_COMMA);

        if ($parser->getLexer()->isNextToken(Lexer::T_NULL)) {
            $parser->match(Lexer::T_NULL);
            $this->values[] = null;
        } else {
            $this->values[] = $parser->ArithmeticExpression();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'IF(%s, %s, %s)',
            $sqlWalker->walkConditionalExpression($this->condition),
            $this->values[0] !== null ? $sqlWalker->walkArithmeticPrimary($this->values[0]) : 'NULL',
            $this->values[1] !== null ? $sqlWalker->walkArithmeticPrimary($this->values[1]) : 'NULL'
        );
    }

}