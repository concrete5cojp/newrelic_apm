<?php
namespace Concrete\Package\NewrelicApm;

use Config;
use Events;

class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'newrelic_apm';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion = '0.2';
    protected $pkgAutoloaderMapCoreExtensions = true;

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
        if (!extension_loaded('newrelic')){
            throw new \Exception(t('The newrelic php extension must be installed as a prerequisite. See %sHow to Setup%s', '<a href="http://www.concrete5.org/marketplace/addons/new-relic-apm/how-to-setup/">', '</a>'));
        }
       parent::install();
    }
    
    public function on_start()
    {
        if (extension_loaded('newrelic')) {
            $site = (Config::get('newrelic.site')) ? Config::get('newrelic.site') : 'concrete5';
            newrelic_set_appname($site);
            
            Events::addListener('on_page_view', function ($event) {
                $request = $event->getRequest();
                newrelic_name_transaction($request->getPathInfo());
            });
        }
    }
}
