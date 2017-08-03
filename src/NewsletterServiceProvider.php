<?php

namespace NHK\MailChimpNewsletter;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mailchimp.php', 'nhk-mailchimp');

        $this->publishes([
            __DIR__.'/../config/mailchimp.php' => config_path('mailchimp.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(Newsletter::class, function () {
            $mailChimp = new Mailchimp(config('nhk-mailchimp.apiKey'));

            $mailChimp->verify_ssl = config('nhk-mailchimp.ssl', true);

            $configuredLists = NewsletterListCollection::createFromConfig(config('nhk-mailchimp'));

            return new Newsletter($mailChimp, $configuredLists);
        });

        $this->app->alias(Newsletter::class, 'nhk-mailchimp');
    }
}
