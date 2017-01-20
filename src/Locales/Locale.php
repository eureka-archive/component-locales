<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Locales;

/**
 * Class Locale
 *
 * @author  Romain Cottard
 */
class Locale
{
    /**
     * @var array $locales List of locales string (main & variants).
     */
    private $locales = [];

    /**
     * Locale constructor.
     *
     * @param string|string[] $locales
     */
    public function __construct($locales)
    {
        if (!is_array($locales) && !is_string($locales)) {
            throw new \InvalidArgumentException('Locale parameter must be an array of string or a string.');
        }

        if (!is_array($locales)) {
            $locales = [$locales];
        }

        foreach ($locales as $locale) {
            if (!is_string($locale)) {
                throw new \InvalidArgumentException('Each locale must be a string.');
            }

            $this->locales[$locale] = $locale;
        }
    }

    /**
     * Use this locale for given category.
     * By default, set for all categories.
     *
     * @param  mixed $category LC_* constant must be used here.
     * @return $this
     */
    public function useLocale($category = LC_ALL)
    {
        setlocale($category, $this->locales);

        return $this;
    }
}