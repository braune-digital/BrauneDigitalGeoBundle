# BrauneDigitalGeoBundle
Symfony Bundle providing integregation for geonames.org and administration in SonataAdmin.
## Features
* Administration in SonataAdmin
    * Sync Countries and Cities with Geonames.org

## Requirements
* JMSSerializerBundle
* BrauneDigitalTranslationBaseBundle
* SonataAdminBundle
* DoctrineORM
  
## Installation
Download using composer:
```bash
composer require braune-digital/geo-bundle
```

And enable the Bundle in your AppKernel:

```php
public function registerBundles()
    {
        $bundles = array(
          ...
          new JMS\SerializerBundle\JMSSerializerBundle(),
          new Sonata\AdminBundle\SonataAdminBundle(),
          new BrauneDigital\TranslationBaseBundle\BrauneDigitalTranslationBaseBundle,
          new BrauneDigital\GeoBundle\BrauneDigitalGeoBundle(),
          ...
        );
```

## Configuration
This Bundle needs the parameter ```geonames_user```, to authenticate the geonames api calls.

## Extend the bundle
This Bundle relies on the Extension in the Application-Namespace. Easiset way is to use the SonataEasyExtendsBundle. Just run:
```
php app/console sonata:easy-extends:generate --dest=src BrauneDigitalGeoBundle
```
And add the extended Bundle to your Kernel:
```php
public function registerBundles()
    {
        $bundles = array(
          ...
          new Application\BrauneDigital\GeoBundle\BrauneDigitalGeoBundle(),
          ...
        );
```
