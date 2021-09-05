<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignedTask;
use App\Models\Step;
use Illuminate\Http\Request;
use Validator;

class AssignedTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignedTask = AssignedTask::with('steps')->where('user_id', auth()->user()->id)->get();
        return response()->json($assignedTask);
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
            'assigned_to' => 'required|numeric',
            'steps.*.title' => 'required|min:3|max:55',
            'steps.*.description' => 'required|min:10|max:100',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $assignedTask = auth()->user()->assignedTasks()->create($request->all());

        foreach ($request->steps as $step) {
            $assignedTask->steps()->create($step);
        }

        return response()->json([
            'message' => 'Assigned Task successfully assigned',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AssignedTask $assignedTask)
    {
        $data = $assignedTask->with('steps')->get();
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignedTask $assignedTask)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|numeric',
            'steps.*.title' => 'required|min:3|max:55',
            'steps.*.description' => 'required|min:10|max:100',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $assignedTask->update([
            'assigned_to' => $request->assigned_to,
        ]);

        $collectionId = array();

        foreach ($request->steps as $step) {
            if ($step['id'] ?? null) {
                Step::where('id', $step['id'])->update([
                    'title' => $step['title'],
                    'description' => $step['description'],
                ]);
                array_push($collectionId, $step['id']);
            } else {
                $stp = $assignedTask->steps()->create($step);
                array_push($collectionId, $stp['id']);
            }
        }

        foreach ($assignedTask->steps as $step) {
            if (!in_array($step['id'], $collectionId)) {
                Step::Find($step['id'])->delete();
            }
        }

        return response()->json([
            'message' => 'Assigned task succesfully updated',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssignedTask $assignedTask)
    {
        $assignedTask->delete();
        return response()->json([
            'message' => 'Assigned Task successfully deleted',
        ]);
    }

    public function completed(Request $request, AssignedTask $assignedTask)
    {
        $assignedTask->update([
            'completed' => $request->completed,
        ]);

        return response()->json([
            'message' => $request->completed ? 'Completed' : 'Incomplete',
        ]);
    }

    public function myAssignedTask()
    {
        $taskList = AssignedTask::with('steps')->where('assigned_to', auth()->user()->id)
            ->where('completed', 0)
            ->get();
        return response()->json($taskList);
    }

}
