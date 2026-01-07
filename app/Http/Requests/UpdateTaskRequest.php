<?php

namespace App\Http\Requests;

use App\Rules\TaskRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-tasks');
    }

    public function rules(): array
    {
        return [
            'title' => TaskRules::title(),
            'description' => TaskRules::description(),
            'project_id' => ['nullable', 'exists:projects,id'],
            'client_name' => TaskRules::clientName(),
            'due_date' => TaskRules::dueDate($this->route('task')->status),
            'status' => TaskRules::status(),
            'priority' => TaskRules::priority(),
            'assigned_users' => TaskRules::assignedUsers(),
            'assigned_users.*' => ['exists:users,id'],
            'tags' => TaskRules::tags(),
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
