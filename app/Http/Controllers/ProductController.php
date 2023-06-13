<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        return view('product.index', [
            'products' => Product::all()
        ]);
    }

    public function checkout()
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $lineItems = [];
        $totalPrice = 0;
        foreach (Product::all() as $product) {
            $totalPrice += $product->price;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $product->name,
                        'images' => [$product->image]
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('checkout.cancel', [], true),
        ]);

        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $totalPrice;
        $order->session_id = $checkout_session->id;
        $order->save();

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        try {
            $session = $stripe->checkout->sessions->retrieve($request->get('session_id'));
            if (!$session) throw new NotFoundHttpException();
            $customer = $stripe->customers->retrieve($session->customer);

            $order = Order::where('session_id', $session->id)->first();
            if (!$order) throw new NotFoundHttpException();
            if ($order && $order->status == 'unpaid') {
                $order->status = 'paid';
                $order->save();
            }
            return view('checkout.success', ['customer' => $customer]);
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }

    public function webhook()
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // If you are testing your webhook locally with the Stripe CLI you
        // can find the endpoint's secret by running `stripe listen`
        // Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                $paymentId = $paymentIntent->id;
                $order = Order::where('session_id', $paymentId)->first();
                if ($order && $order->status == 'unpaid') {
                    $order->status = 'paid';
                    $order->save();
                }
                handlePaymentIntentSucceeded($paymentIntent);
                break;
                // case 'payment_intent.succeeded':
                //     $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                //     handlePaymentIntentSucceeded($paymentIntent);
                //     break;
                // case 'payment_method.attached':
                //     $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
                //     handlePaymentMethodAttached($paymentMethod);
                //     break;
                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        http_response_code('');
    }
}
