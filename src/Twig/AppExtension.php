<?php

namespace App\Twig;

use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    protected $assetExtension ;
    public function __construct(AssetExtension $assetExtension)
    {
        $this->assetExtension = $assetExtension ;
    }

    /*
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }
    */

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_upload_asset', [$this, 'uploadAsset']),
        ];
    }

    public function uploadAsset(string $field =null , string $fieldAlternative = null )
    {
        if ($field === null && $fieldAlternative !== null ) {
            return $this->assetExtension->getAssetUrl($fieldAlternative) ;
        }
        return $field ;
    }
}
