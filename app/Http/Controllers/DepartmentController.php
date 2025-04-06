<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Voting;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function list()
    {
        $user = auth()->user();

        $departments = Department::all();

        foreach ($departments as $department) {
            $department->question_count = $department->questions()->count();
        }

        return response()->json($departments);
    }
}
