<?php

namespace App\Enums\NowPayments;

enum PaymentStatus: string
{
    case Waiting = 'waiting'; // waiting for the customer to send the payment. The initial status of each payment
    case Confirming = 'confirming'; // the transaction is being processed on the blockchain.
    //Appears when NOWPayments detect the funds from the user on the blockchain
    case Confirmed = 'confirmed'; // the process is confirmed by the blockchain. Customer’s funds have accumulated enough confirmations
    case Sending = 'sending'; // the funds are being sent to your personal wallet. We are in the process of sending the funds to you
    case PartiallyPaid = 'partially_paid'; // it shows that the customer sent the less than the actual price. Appears when the funds have arrived in your wallet
    case Finished = 'finished'; // the funds have reached your personal address and the payment is finished
    case Failed = 'failed'; // the payment wasn't completed due to the error of some kind
    case Refunded = 'refunded'; // the funds were refunded back to the user
    case Expired = 'expired'; // the user didn't send the funds to the specified address in the 7 days time window
}
