<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::where('user_id', auth()->user()->id)
        ->orderBy('is_complete', 'asc')
        ->orderBy('created_at', 'desc')
        ->get();
        // dd($todos);
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
        ->where('is_complete', true)
        ->count();
        return view('todo.index', compact('todos', 'todosCompleted'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('todo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);

        $todo = new Todo;
        $todo->title = $request->title;
        $todo->user_id = auth()->user()->id;
        $todo->save();


        DB::table('todos')->insert([
            'title' => $request->title,
            'user_id' =>auth()->user()->id,
            'created_at' =>now(),
            'updated_at' =>now(),

        ]);
        $todo = Todo::create([
            'title' => ucfirst($request->title),
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('todo.index')->with('success', 'Todo Created Succcessfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        if(auth()->user()->id == $todo->user_id){
            //dd($todo);

            return view('todo.edit', compact('todo'));
        }else{
            // abort(403);
            //abort(403, 'Not authorized');

            return redirect()->route('todo.index')->with('danger', 'You are not authorized to edit this todo!');
        }
    }

    public function complete(Todo $todo)
    {
        if(auth()-> user()->id == $todo->user_id){
            $todo->update([
                'is_complete' => true,
            ]);

            return redirect()->route('todo.index')->with('success', 'Todo completed successfully!');

        }else{
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to complete this todo!');

        }
    }

    public function uncomplete(Todo $todo)
    {
        if(auth()-> user()->id == $todo->user_id){
            $todo->update([
                'is_complete' => false,
            ]);

            return redirect()->route('todo.index')->with('success', 'Todo uncompleted successfully!');

        }else{
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to uncomplete this todo!');

        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);



        //Practical
        //$todo->title = $request->title;
        //$todo->save();

        //Eloquent Way - Readable

        $todo->update([
            'title' => ucfirst($request->title),
        ]);
        return redirect()->route('todo.index')->with('success', 'Todo Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        if(auth()->user()->id == $todo->user_id){

            $todo->delete();
            return redirect()->route('todo.index')->with('success', 'Todo deleted successsfully!');

        }else{
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to delete this todo!');
        }
    }

    public function destroyCompleted(){
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
        ->where('is_complete', true)
        ->get();
        foreach($todosCompleted as $todo){
            $todo->delete();
        }

        return redirect()->route('todo.index')->with('success', 'All Completed todos deleted successfully!');
    }
}
