<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            ' ' => new InvoiceResource($this->invoice),
            'customer_user_id' => $this->customer_user_id,
            'customer' => new UserChartResource($this->customer),
            'customer_email' => $this->customer_email,
            'payment_name' => $this->payment_name,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'authorization_url' => $this->authorization_url,
            'access_code' => $this->access_code,
            'reference' => $this->reference,
            'status' => $this->status,
            'gateway_response' => $this->gateway_response,
            'charge_attempted' => $this->charge_attempted,
            'transaction_date' => $this->transaction_date,
            'save_card_auth' => $this->save_card_auth,
            'is_flutterwave' => $this->is_flutterwave,
            'link' => $this->link,
            'client_name' => $this->client_name,
            'client_id' => $this->client_id,
            'client' => new ClientChartResource($this->client),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
