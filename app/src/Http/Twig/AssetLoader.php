<?php

namespace App\Http\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetLoader extends AbstractExtension
{



    public function __construct(private bool $isDev, private string $manifest) {}




    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', [$this, 'asset'], ['is_safe' => ['html']])
        ];
    }


    public function asset(string $entry): string
    {

        return $this->isDev ? $this->assetDev($entry) : $this->assetProd($entry);
    }



    public function assetDev(string $entry): string
    {

        $html = <<<HTML
        <script type="module" defer src="http://localhost:5173/@vite/client"></script>
        <script type="module" defer src="http://localhost:5173/{$entry}"></script>
HTML;
        return $html;
    }


    public function assetProd(string $entry): string
    {
        $manifestData = json_decode(file_get_contents($this->manifest), true);
        $file = $manifestData[$entry]['file'];
        $css = $manifestData[$entry]['css'] ?? [];
        $html = <<<HTML
<script type="module" src="/dist/{$file}" defer></script>
<link rel="stylesheet" media="screen" href="/dist/css/main.css"/>
<link rel="stylesheet" media="screen" href="/dist/css/app.css"/>

HTML;
        foreach ($css as $cssFile) {
            if ($cssFile === 'css/app.css' || $cssFile === 'css/main.css') {
                continue;
            }
            $html .= <<<HTML
<link rel="stylesheet" media="screen" href="/dist/{$cssFile}"/>
HTML;
        }

        return $html;
    }
}
