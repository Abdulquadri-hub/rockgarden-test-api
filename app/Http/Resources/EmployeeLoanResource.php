<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLoanResource extends JsonResource
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
            'name' => $this->name,
            'currency' => $this->currency,
             'amount' => $this->amount,
            'disbursement_date' => $this->disbursement_date,
             'repayment_start_date' => $this->repayment_start_date,
            'installment_amount' => $this->installment_amount,
             'reason' => $this->reason,
            'status' => $this->status,
            'overdue_date' => $this->overdue_date,
            'interest_rate' => $this->interest_rate,
            'total_amount_paid' => $this->total_amount_paid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
