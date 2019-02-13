<?php

function checkAccessAndRedirect($locationAccessSuccessful, $locationAccessUnsuccessful)
{
    if (isset($_SESSION['username'])) {
        if (!empty($_SESSION['username'])) {
            if ($locationAccessSuccessful != NULL) {
                header("Location: {$locationAccessSuccessful}");
            }
        } else {
            if ($locationAccessUnsuccessful != NULL) {
                header("Location: {$locationAccessUnsuccessful}");
            }
        }
    } else {
        if ($locationAccessUnsuccessful != NULL) {
            header("Location: {$locationAccessUnsuccessful}");
        }
    }
}