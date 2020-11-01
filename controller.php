<?php
namespace Concrete\Package\NewrelicApm;

use Concrete\Core\Http\Request;
use Concrete\Core\Package\Package;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Controller extends Package
{
    protected $pkgHandle = 'newrelic_apm';
    protected $appVersionRequired = '8.5.2';
    protected $pkgVersion = '0.4';

    public function getPackageName()
    {
        return t('Transaction fix for New Relic APM');
    }

    public function getPackageDescription()
    {
        return t('A helpful micro package for monitoring your concrete5 site with New Relic.');
    }

    public function install()
    {
        if (!extension_loaded('newrelic')) {
            throw new RuntimeException(t('The newrelic php extension must be installed as a prerequisite. See %sHow to Setup%s', '<a href="http://www.concrete5.org/marketplace/addons/new-relic-apm/how-to-setup/">', '</a>'));
        }
        parent::install();
    }

    public function on_start()
    {
        if (extension_loaded('newrelic')) {
            $site = $this->app->make('site')->getSite();
            $appConfig = $this->app->make('config');
            $site = ($appConfig->get('newrelic.site')) ? $appConfig->get('newrelic.site') : tc('SiteName', $site->getSiteName());
            newrelic_set_appname($site);

            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $this->app->make(EventDispatcherInterface::class);
            $dispatcher->addListener('on_before_dispatch', function () {
                $request = Request::getInstance();
                newrelic_name_transaction($request->getPath());
            });
        }
    }
}
