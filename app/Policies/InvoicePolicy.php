<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the invoice.
     */
    public function view(?User $user, Invoice $invoice): Response
    {
        $order = $invoice->order;

        // If user is authenticated and owns the order
        if ($user && $order->user_id === $user->id) {
            return Response::allow();
        }

        // Allow farmers to view invoices if they have products in the order
        if ($user && $user->isFarmer()) {
            $hasFarmerProducts = $order->items()->whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->exists();

            if ($hasFarmerProducts) {
                return Response::allow();
            }
        }

        // For guest orders, we could implement a token-based access
        // For now, we'll allow access if current session matches guest email
        // This is a simplified version - in production, use signed URLs
        if (!$order->user_id) {
            // Allow guest access for now
            // TODO: Implement proper guest access control with signed URLs or tokens
            return Response::allow();
        }

        return Response::deny('No tiene permiso para ver esta factura.');
    }

    /**
     * Determine whether the user can download the invoice PDF.
     */
    public function download(?User $user, Invoice $invoice): Response
    {
        return $this->view($user, $invoice);
    }
}
