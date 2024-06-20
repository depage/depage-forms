<?php
/**
 * @file    elements/url.php
 * @brief   url input element
 *
 * @author Frank Hellenkamp <jonas@depage.net>
 * @author Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace Depage\HtmlForm\Elements;

/**
 * @brief HTML url input-type
 *
 * Class for HTML5 input-type "url".
 *
 * Usage
 * -----
 *
 * @code
    <?php
        $form = new Depage\HtmlForm\HtmlForm('myform');

        // add an URL field
        $form->addUrl('homepage', array(
            'label' => 'Homepage URL',
        ));

        // process form
        $form->process();

        // Display the form.
        echo ($form);
    @endcode
 **/
class Url extends Text
{
    // {{{ setDefaults()
    /**
     * @brief   collects initial values across subclasses
     *
     * The constructor loops through these and creates settable class
     * attributes at runtime. It's a compact mechanism for initialising
     * a lot of variables.
     *
     * @return void
     **/
    protected function setDefaults()
    {
        parent::setDefaults();

        $this->defaults['errorMessage'] = _('Please enter a valid URL');

        // @todo add option to test url by domain name (dns) or also with a request (for http/https urls)
    }
    // }}}

    // {{{ typeCastValue()
    /**
     * @brief   Converts value into htmlDOM
     *
     * @return void
     **/
    protected function typeCastValue()
    {
        $info = parse_url(trim($this->value));

        $scheme   = isset($info['scheme']) ? $info['scheme'] . '://' : '';
        $host     = $info['host'] ?? '';
        $host     = idn_to_ascii($host);
        $port     = isset($info['port']) ? ':' . $info['port'] : '';
        $user     = $info['user'] ?? '';
        $pass     = isset($info['pass']) ? ':' . $info['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $info['path'] ?? '';
        $query    = isset($info['query']) ? '?' . $info['query'] : '';
        $fragment = isset($info['fragment']) ? '#' . $info['fragment'] : '';

        $encodedPath = implode('/', array_map([$this, 'encodeUrlPath'], explode('/', $path)));

        $this->value = "$scheme$user$pass$host$port$encodedPath$query$fragment";
    }
    // }}}
    // {{{ encodeUrlPath()
    /**
     * @brief   Encodes the path of an URL
     *
     * @param   string $path
     *
     * @return  string
     **/
    protected function encodeUrlPath($path)
    {
        if (rawurlencode($path) != str_replace(['%','+'], ['%25','%2B'], $path)) {
            $path = rawurlencode($path);
        }

        return $path;
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
