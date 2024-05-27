<?php
namespace App\Services;

use Xendit\Xendit; // Add this line

class XenditService
{
    protected $xendit;

    public function __construct()
    {
        $this->xendit = new Xendit(config('services.xendit.secret_key'));
    }

    public function createInvoice($externalId, $amount, $payerEmail, $description, $successRedirectUrl, $failureRedirectUrl)
    {
        return $this->xendit->createInvoice([
            'external_id' => $externalId,
            'amount' => $amount,
            'payer_email' => $payerEmail,
            'description' => $description,
            'success_redirect_url' => $successRedirectUrl,
            'failure_redirect_url' => $failureRedirectUrl,
        ]);
    }

}
