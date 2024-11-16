<?php

namespace App\Interfaces;

interface NewsProviderInterface{
    public function fetchNews(): array;
}
