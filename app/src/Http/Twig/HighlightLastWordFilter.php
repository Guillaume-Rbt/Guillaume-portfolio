<?php

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HighlightLastWordFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight_last_word', [$this, 'highlightLastWord'], ['is_safe' => ['html']]),
        ];
    }

    public function highlightLastWord(string $text,  string $class = 'text-light-blue'): string
    {
        $words = explode(' ', $text);
        $lastWord = array_pop($words);
        return implode(' ', $words) . ' <span class="' . $class . '">' . $lastWord . '</span>';
    }
}
