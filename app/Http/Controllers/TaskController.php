<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /tasks — список всех задач
    public function index()
    {
        return response()->json(Task::all());
    }

    // POST /tasks — создание новой задачи
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => [
            'required', // обязательно
            'string',   // должно быть строкой
            'min:1',    // минимум 1 символ
            'max:255'   // максимум 255 символов
        ],
        'description' => [
            'nullable', // может отсутствовать или быть null
            'string',   // если есть — должно быть строкой
            'max:1000'  // ограничение на длину
        ],
        'status' => [
            'required',
            'string',
            'in:pending,in_progress,completed' // только эти значения
        ]
    ], [
        // Кастомные сообщения (опционально)
        'title.required' => 'Поле "title" обязательно для заполнения.',
        'title.min' => 'Поле "title" должно содержать хотя бы один символ.',
        'status.in' => 'Недопустимое значение статуса. Допустимые: pending, in_progress, completed.'
    ]);

    $task = Task::create($validated);

    return response()->json($task, 201);
}

    // GET /tasks/{id} — просмотр одной задачи
    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task);
    }

    // PUT /tasks/{id} — обновление задачи
    public function update(Request $request, $id)
{
    $task = Task::find($id);

    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }

    $validated = $request->validate([
        'title' => [
            'required',
            'string',
            'min:1',
            'max:255'
        ],
        'description' => [
            'nullable',
            'string',
            'max:1000'
        ],
        'status' => [
            'required',
            'string',
            'in:pending,in_progress,completed'
        ]
    ], [
        'title.required' => 'Поле "title" обязательно для заполнения.',
        'title.min' => 'Поле "title" не может быть пустым.',
        'status.in' => 'Недопустимое значение статуса.'
    ]);

    $task->update($validated);

    return response()->json($task);
}

    // DELETE /tasks/{id} — удаление задачи
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}
