<?php

namespace App\Controllers;

use App\DatabaseService;
use App\ArticlesView;

class BaseController
{
    protected DatabaseService $dbService;
    protected ArticlesView $articlesView;

    public function __construct(DatabaseService $dbService, ArticlesView $articlesView)
    {
        $this->dbService = $dbService;
        $this->articlesView = $articlesView;
    }
}
