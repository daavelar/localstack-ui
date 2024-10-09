<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('queues', function () {
    return true;
});
