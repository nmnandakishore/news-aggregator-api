<?php

use App\Services\NewsFetcher;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule as Schedule;

Schedule::call(new NewsFetcher)->everySecond();
