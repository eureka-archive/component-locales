<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Locales\Gettext;

/**
 * Class Domain
 *
 * @author  Romain Cottard
 */
class Domain
{
    /**
     * @var string $domain Domain name for translations.
     */
    private $domain = '';

    /**
     * @var string $pathToTranslations Path for the translations files.
     */
    private $pathToTranslations = '';

    /**
     * Locale constructor.
     *
     * @param string $locales
     */
    public function __construct($domain, $path, $charset = 'UTF-8')
    {
        $this->setDomain($domain);
        $this->setPathToTranslations($path);
        $this->setCharset($charset);

        $this->init();
    }

    /**
     * Use this domain for gettext functions.
     * @return $this
     */
    public function useDomain()
    {
        textdomain($this->domain);

        return $this;
    }

    /**
     * Set domain message.
     *
     * @param  string $domain
     * @return $this
     */
    private function setDomain($domain)
    {
        if (!is_string($domain)) {
            throw new \InvalidArgumentException('Gettext domain must be a valid string.');
        }

        $this->domain = $domain;

        return $this;
    }

    /**
     * Set path to the translations
     * @param  string $pathToTranslations
     * @return $this
     */
    private function setPathToTranslations($pathToTranslations)
    {
        if (!is_string($pathToTranslations)) {
            throw new \InvalidArgumentException('Path for translations must be a valid string.');
        }

        if (!is_dir($pathToTranslations)) {
            throw new \InvalidArgumentException('Given path does not exist!');
        }

        $this->pathToTranslations = $pathToTranslations;

        return $this;
    }

    /**
     * Set charset.
     *
     * @param  string $charset
     * @return $this
     */
    private function setCharset($charset)
    {
        if (!is_string($charset)) {
            throw new \InvalidArgumentException('Charset parameter must be a valid string.');
        }

        $this->charset = $charset;

        return $this;
    }

    /**
     * Initialize domain.
     *
     * @return void
     */
    private function init()
    {
        bindtextdomain($this->domain, $this->pathToTranslations);
        bind_textdomain_codeset($this->domain, $this->charset);
    }
}