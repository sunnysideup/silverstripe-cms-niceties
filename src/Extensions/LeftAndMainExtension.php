<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use Sunnysideup\CMSNiceties\Interfaces\BrandColourProvider;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Class \Sunnysideup\CMSNiceties\Extensions\LeftAndMainExtension.
 *
 * @property LeftAndMainExtension $owner
 */
class LeftAndMainExtension extends Extension
{
    public function init()
    {
        Requirements::add_i18n_javascript('sunnysideup/cms-niceties: client/lang');
    }

    private static array $brand_colours = [
        'Light' => '',
        'Dark' => '',
        'Font' => '',
    ];

    public function updateClientConfig($clientConfig)
    {
        $brandColourProviders = ClassInfo::implementorsOf(BrandColourProvider::class);
        if($brandColourProviders) {
            foreach($brandColourProviders as $provider) {
                $providerInstance = new $provider();
                $brandColours = $providerInstance->getBrandColours();
                if($brandColours) {
                    $clientConfig['brand_colours'] = $brandColours;
                    break;
                }
            }
        } else {
            $clientConfig['brand_colours'] = $this->owner->config()->get('brand_colours');
        }
        $light = $clientConfig['brand_colours']['Light'] ?? '';
        $dark = $clientConfig['brand_colours']['Dark'] ?? '';
        $font = $clientConfig['brand_colours']['Font'] ?? '';
        if ($light && $dark && $font) {
            Requirements::customCSS(
                '
                #cms-menu,
                #cms-menu *,
                #cms-menu a:link,
                #cms-menu a:visited,
                #cms-menu input::placeholder,
                #cms-menu .flexbox-area-grow
                {
                    background-color: ' . $light . ';
                    color: ' . $font . ';
                    border-color: #fff;
                }
                #cms-menu .cms-menu__list > li.current.children > a,
                #cms-menu .cms-menu__list > li.current.children > a * {
                    background-color: ' . $dark . '!important;
                    color: #fff!important;
                }
                #cms-menu .cms-menu__list > .current.children,
                #cms-menu .cms-menu__list > .current.children *,
                #cms-menu .cms-menu__list > .link.children > a,
                #cms-menu .cms-menu__list > .link.children > a  {
                    background-color: ' . $light . '!important;
                    color: ' . $font . '!important;
                }
                #cms-menu .cms-menu__header,
                #cms-menu .cms-menu__header *,
                #cms-menu .cms-menu__header .cms-sitename,
                #cms-menu .cms-menu__header .cms-login-status,
                #cms-menu .toolbar,
                #cms-menu .toolbar * {
                    background-color: ' . $dark . '!important;
                    color: #fff!important;
                }

                #cms-menu li a:hover {
                    filter: brightness(110%);
                }
                #cms-menu li.current a {
                    filter: brightness(105%);
                }
                .breadcrumbs-wrapper,
                .breadcrumbs-wrapper *
                {
                    color: ' . $dark . ';
                }


                ',
                'LeftAndMainExtensionCMSNiceties'
            );
        }
    }
}
