<?php

namespace App\Http\Resources;

use App\Models\Allowance;
use App\Models\Bonus;
use App\Models\Deduction;
use App\Models\PayRunBonuses;
use App\Models\PayRunLoan;
use App\Models\Tax;
use Illuminate\Http\Resources\Json\JsonResource;

class PayRunResource extends JsonResource
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
            'staff' => new EmployeeChartResource($this->staff),
//            'deductions' => Deduction::whereIn('name', $this->deductions)->get(),
//            'allowances' => Allowance::whereIn('name', $this->allowances)->get(),
//            'taxes' => Tax::whereIn('name', $this->taxes)->get(),
//            'bonuses' => Bonus::whereIn('name', $this->bonuses)->get(),
            'deductions' => $this->deductions,
            'allowances' => $this->allowances,
            'taxes' => $this->taxes,
            'bonuses' => $this->bonuses,
            'currency' => $this->currency,
            'title' => $this->title,
            'from_date' => $this->from_date,
            'basic_salary' => $this->basic_salary,
            'days_present' => $this->days_present,
            'to_date' => $this->to_date,
            'designation' => $this->designation,
            'staff_name' => $this->staff_name,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'department' => $this->department,
            'duty_type' => $this->duty_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
