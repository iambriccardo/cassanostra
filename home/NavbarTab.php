<?php

/**
 * Class NavbarTab
 * Rappresenta una tab della navbar, con testo e pagina corrispondenti
 */
class NavbarTab
{
    private $name;  // Nome della tab da mostrare nella pagina
    private $page;  // Nome della pagina PHP con il contenuto della tab (estensione compresa)

    function __construct($name, $page)
    {
        $this->name = $name;
        $this->page = $page;
    }

    function name()
    {
        return $this->name;
    }

    function page()
    {
        return $this->page;
    }
}