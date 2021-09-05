<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = auth()->user()->tasks;

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:50',
            'description' => 'required|min:3|max:255',
            'deadline' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        auth()->user()->tasks()->create($request->all());

        return response()->json([
            'message' => 'Task successfully added',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return response()->json([
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:50',
            'description' => 'required|min:3|max:255',
            'deadline' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Task Successfully updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json([
            'message' => 'Task succesfully deleted',
        ]);
    }

    public function completed(Request $request, Task $task)
    {
        $task->update([
            'completed' => $request->completed,
        ]);

        return response()->json([
            'message' => $request->completed ? 'Task Completed' : 'Task Incomplete',
        ]);
    }
}
