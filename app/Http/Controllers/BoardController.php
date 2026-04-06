<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoardController extends Controller
{
 public function index()
{
    $tasks = \App\Models\Task::all()->groupBy('status');

    return view('boards.index', compact('tasks'));
}
}
