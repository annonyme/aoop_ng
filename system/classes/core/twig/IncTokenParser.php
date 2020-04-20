<?php
namespace core\twig;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class IncTokenParser extends AbstractTokenParser {
    /**
     * @var Parser
     */
    protected $parser;

    private $list = [];

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function getTag(): string
    {
        return 'base_include';
    }

    /**
     * @param Token $token
     *
     * @return Node
     * @throws SyntaxError
     */
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();
        $template = $stream->next()->getValue();

        $newBase = TwigUtils::findNewBase($template, $this->list, null);
        $parent = '@' . $newBase . '/' . $template;

        $stream->next();

        $stream->injectTokens([
            new Token(Token::BLOCK_START_TYPE, '', 2),
            new Token(Token::NAME_TYPE, 'include', 2),
            new Token(Token::STRING_TYPE, $parent, 2),
            new Token(Token::BLOCK_END_TYPE, '', 2),
        ]);

        return new Node();
    }
}
