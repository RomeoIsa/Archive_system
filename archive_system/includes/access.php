<?php

function canViewFile($file, $session_user_id, $session_institution_id) {

    if ($file['user_id'] == $session_user_id) {
        return true;
    }

    if ($file['visibility'] === 'public') {
        return true;
    }

  
    if (
        $file['visibility'] === 'institution' &&
        $file['institution_id'] == $session_institution_id
    ) {
        return true;
    }


    return false;
}