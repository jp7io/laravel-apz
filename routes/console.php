<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('weather:update')->hourly();
