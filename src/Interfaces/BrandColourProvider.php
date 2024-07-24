<?php

namespace Sunnysideup\CMSNiceties\Interfaces;

interface BrandColourProvider
{
    /**
     * needs to return an array with the following
     * 'Light' => '#333333',
     * 'Dark' => '#222222',
     * 'Font' => '#111111',
     *
     * @return array
     */
    public function getBrandColours(): array;
}
