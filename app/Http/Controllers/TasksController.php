<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Validator;

class TasksController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        //return $request->input('all');
        if($request->input('status')=='all'){
            $tasks = Task::with('user')->withTrashed()->get();
        }else{
            $tasks = Task::with('user')->get();
        }
        return response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:tasks',
            'user_id' => 'required',

        ],[
            'name.required' => 'Name is required.',
            'name.unique' => 'Name has already been taken.',
            'user_id.required' => 'User is required.',
        ]);

        $name = $request->input('name');
        $user_id = 1;
        $taskData = ['name' => $name, 'user_id' => $user_id];
        $task = Task::create($taskData);

        $data = [
            'status' => 'success',
            'message'=>'Task created successfully',
        ];
        //return $this->show($task->id);
        return response()->json($data);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try {
            $task = Task::find($id);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 401);
        }

        $data = [
            'status' => 'success',
            'task' => $task,
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        Task::onlyTrashed()->where('id',$id)->forceDelete();
        $data = [
            'status'=>'success',
            'message'=>'Task Deleted successfully!'
        ];
        return response()->json($data);
    }
    public function completed(Request $request){
        $id = $request->input('task_id');
        Task::find($id)->delete();
        $data = [
            'status'=>'success',
            'message'=>'Task Completed successfully!'
        ];
        return response()->json($data);
    }
}
