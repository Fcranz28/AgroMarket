<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Modules\Identity\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the invoice.
     *
     * Guest orders are never visible through the authenticated routes; guests
     * download their invoice through a signed URL (invoice.guest-download).
     */
    public function view(User $user, Invoice $invoice): Response
    {
        $order = $invoice->order;

        if ($user->isAdmin()) {
            return Response::allow();
        }

        if ($order->user_id !== null && $order->user_id === $user->id) {
            return Response::allow();
        }

        if ($user->isFarmer()) {
            $hasFarmerProducts = $order->items()
                ->whereHas('product', fn ($query) => $query->where('user_id', $user->id))
                ->exists();

            if ($hasFarmerProducts) {
                return Response::allow();
            }
        }

        return Response::deny('No tiene permiso para ver esta factura.');
    }

    /**
     * Determine whether the user can download the invoice PDF.
     */
    public function download(User $user, Invoice $invoice): Response
    {
        return $this->view($user, $invoice);
    }
}
