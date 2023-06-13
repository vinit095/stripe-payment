# Stripe payment with laravel

## About this project

This project is an demo of [Stripe](https://stripe.com/en-in) payment and payment webhook. The payment mode used is "Testing/Sandbox".

### Package used

-   **[Stripe-php](https://github.com/stripe/stripe-php)**
-   **[akaunting/laravel-money](https://github.com/akaunting/laravel-money)**
-   **[Tailwindcss](https://tailwindcss.com/docs/guides/laravel)**

## Commands

** Run the following commands **

```
npm install
php artisan migrate
php artisan serve
npm run dev
```

## Configuration

1. copy and create new '.env' file using '.env.example' file.
2. setup the "STRIPE_SECRET_KEY" in env file.
3. If you want you use ** Stripe payment webhooks ** you can follow the guide [Stripe Webhook](https://stripe.com/docs/webhooks/test) and add the generate "WEBHOOK_SIGNING_SECRET" as "STRIPE_WEBHOOK_SECRET" in .env file.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
