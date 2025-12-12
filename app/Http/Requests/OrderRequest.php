<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidWeight;
use App\Rules\ValidMonetary;
use App\Rules\ValidFutureDate;

class OrderRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'weight' => ['required', new ValidWeight],
            'add_ons' => 'nullable|array',
            'add_ons.*' => 'string|in:stain_removal,fragrance,express_service,premium_care',
            'subtotal' => ['required', new ValidMonetary],
            'discount' => ['required', new ValidMonetary],
            'total_amount' => ['required', new ValidMonetary],
            'amount_paid' => ['required', new ValidMonetary],
            'pickup_date' => ['nullable', 'date', new ValidFutureDate(true, 1)],
            'estimated_finish' => ['required', 'date', new ValidFutureDate(false, 1)],
            'remarks' => 'nullable|string|max:1000',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'service_type' => 'nullable|in:standard,express,premium',
        ];

        // Add status field only for updates
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = 'required|in:pending,approved,rejected,picked_up,washing,drying,folding,quality_check,ready,delivery_pending,completed,cancelled';
            $rules['finished_at'] = 'nullable|date';
            $rules['primary_washer_id'] = 'nullable|exists:machines,id';
            $rules['primary_dryer_id'] = 'nullable|exists:machines,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'weight.required' => 'Please enter the weight of the laundry.',
            'subtotal.required' => 'The subtotal is required.',
            'total_amount.required' => 'The total amount is required.',
            'amount_paid.required' => 'The amount paid is required.',
            'estimated_finish.required' => 'The estimated completion date is required.',
            'estimated_finish.date' => 'Please provide a valid date for estimated completion.',
            'pickup_date.date' => 'Please provide a valid date for pickup.',
            'add_ons.*.in' => 'Invalid add-on selected.',
            'priority.in' => 'Invalid priority level selected.',
            'service_type.in' => 'Invalid service type selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'weight' => 'weight (kg)',
            'subtotal' => 'subtotal',
            'discount' => 'discount',
            'total_amount' => 'total amount',
            'amount_paid' => 'amount paid',
            'pickup_date' => 'pickup date',
            'estimated_finish' => 'estimated completion date',
            'remarks' => 'remarks',
            'priority' => 'priority',
            'service_type' => 'service type',
        ];
    }
}
