<?php

/**
 * Store an alert in the session for the next request.
 *
 * @param  string $message
 * @param  string $type
 * @return void
 */
function alert($message, $type = "success")
{
    request()->session()->flash('alert', [
        'type' => $type,
        'message' => $message
    ]);
}
