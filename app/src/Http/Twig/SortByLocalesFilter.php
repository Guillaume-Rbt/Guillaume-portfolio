<?php

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortByLocalesFilter extends AbstractExtension
{
    private array $locales;

    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sort_by_locales', [$this, 'sortByLocales']),
        ];
    }

    public function sortByLocales(iterable $items): array
    {
        $result = [];
        $itemsByLocale = [];

        // First pass: group items by locale
        foreach ($items as $item) {
            $itemLocale = null;

            // Try to get locale from FormView
            if (isset($item->vars['data'])) {
                $data = $item->vars['data'];
                if (method_exists($data, 'getLocale')) {
                    $itemLocale = $data->getLocale();
                }
            }

            if ($itemLocale === null && method_exists($item, 'getData')) {
                $data = $item->getData();
                if (method_exists($data, 'getLocale')) {
                    $itemLocale = $data->getLocale();
                }
            }

            if ($itemLocale !== null) {
                $itemsByLocale[$itemLocale] = $item;
            }
        }

        // Second pass: return items in the correct locale order
        foreach ($this->locales as $locale) {
            if (isset($itemsByLocale[$locale])) {
                $result[] = $itemsByLocale[$locale];
            }
        }

        return $result;
    }
}
