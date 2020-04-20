<?php
namespace core\twig;

use Twig\Node\Node;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class ExtTokenParser extends AbstractTokenParser {
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
        return 'base_extends';
    }

    /**
     * @param Token $token
     *
     * @return Node
     * @throws \Twig\Error\SyntaxError
     */
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();
        $source = $stream->getSourceContext()->getName();
        $template = $stream->next()->getValue();

        $parts = preg_split("/\//i", preg_replace("/@/", '', $source));
        $newBase = TwigUtils::findNewBase($template, $this->list, $parts[0]);
        $parent = '@' . $newBase . '/' . $template;

        $stream->next();

        $stream->injectTokens([
            new Token(Token::BLOCK_START_TYPE, '', 2),
            new Token(Token::NAME_TYPE, 'extends', 2),
            new Token(Token::STRING_TYPE, $parent, 2),
            new Token(Token::BLOCK_END_TYPE, '', 2),
        ]);

        return new Node();
    }
}
