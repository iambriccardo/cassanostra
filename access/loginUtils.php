<?php

function checkAccessAndRedirect($locationSuccessful, $locationUnsuccessful)
{
    if (isset($_SESSION['username'])) {
        if (!empty($_SESSION['username'])) {
            if ($locationSuccessful != NULL) {
                header("Location: {$locationSuccessful}");
            }
        } else {
            if ($locationUnsuccessful != NULL) {
                header("Location: {$locationUnsuccessful}");
            }
        }
    } else {
        if ($locationUnsuccessful != NULL) {
            header("Location: {$locationUnsuccessful}");
        }
    }
}