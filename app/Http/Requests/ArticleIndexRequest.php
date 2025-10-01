<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'sources' => 'nullable|array',
            'sources.*' => 'string|max:255',
            'category' => 'nullable|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:255',
            'author' => 'nullable|string|max:255',
            'authors' => 'nullable|array',
            'authors.*' => 'string|max:255',
            'authors' => 'nullable|array',
            'authors.*' => 'string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'sort' => 'nullable|in:newest,oldest,title',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'to.after_or_equal' => 'The to date must be after or equal to the from date.',
            'per_page.max' => 'The per page field may not be greater than 100.',
            'sort.in' => 'The sort field must be one of: newest, oldest, title.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert comma-separated strings to arrays
        $this->merge([
            'sources' => $this->convertToArray($this->input('sources')),
            'categories' => $this->convertToArray($this->input('categories')),
            'authors' => $this->convertToArray($this->input('authors')),
        ]);
    }

    /**
     * Convert input to array if it's a comma-separated string.
     */
    private function convertToArray($input): ?array
    {
        if (is_null($input)) {
            return null;
        }
        
        if (is_array($input)) {
            return array_filter($input);
        }
        
        if (is_string($input)) {
            return array_filter(array_map('trim', explode(',', $input)));
        }
        
        return null;
    }
}
