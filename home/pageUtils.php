<?php

function printNavbar()
{
    echo '
    <nav class="nav-extended blue-grey darken-4">
        <div class="nav-wrapper">
            <a class="brand-logo center">CassaNostra</a>
            <ul id="nav-mobile" class="right">
                <li><a href="?logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    ';
}