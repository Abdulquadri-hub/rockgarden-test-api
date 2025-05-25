<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePayRunResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'staff_id' => $this->shift_id,
            'staff' => new EmployeeChartResource($this->staff),
            'basic_salary' => $this->basic_salary,
            'basic_salary_currency' => $this->basic_salary_currency,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'deductions' => $this->deductions,
            'reimbursement' => $this->reimbursement,
            'reimbursement_currency' => $this->reimbursement_currency,
            'reimbursement_info' => $this->reimbursement_info,
            'pay_days' => $this->pay_days,
            'allowances' => $this->allowances,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
