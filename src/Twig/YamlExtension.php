<?php

namespace App\Twig;

use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class YamlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
           // new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('parseYaml', [$this, 'parseYaml']),
        ];
    }

    public function parseYaml(string $file): array|null
    {
        $publicRoot = realpath(__DIR__ . '/../../public');

        if (!str_starts_with($file, '/')) {
            $publicRoot .= '/';
        }

        $file = $publicRoot . $file;

        if (!file_exists($file)) {
            return null;
        }

        return Yaml::parseFile($file);
    }
}
