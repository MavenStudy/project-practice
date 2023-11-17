<?php
namespace Maven\ProjectPractice\Blog\Http\Actions;

use Maven\ProjectPractice\Blog\Http\Request;
use Maven\ProjectPractice\Blog\Http\Response;

interface ActionInterface{
    public function handle(Request $request):Response;
}