<?php


namespace BrauneDigital\GeoBundle\Admin;

use BrauneDigital\TranslationBaseBundle\Admin\TranslationAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CountryAdmin extends TranslationAdmin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

		$this->setCurrentLocale();
		$this->buildTranslations($this->subject);

        $formMapper
            ->with('Translation')
                ->add('translations', 'a2lix_translations', array(
                    'locales' => $this->currentLocale,
                    'required_locales' => $this->currentLocale,
                    'fields' => array(
                        'name' => array(
                            'field_type' => 'text',
                            'required' => false,
                            'label' => 'Name',
                            'disabled' => false,
                            'empty_data' => '',
                        ),
                        'seoDescription' => array(
                            'field_type' => 'text',
                            'required' => false,
                            'label' => 'SEO description',
                            'empty_data' => '',
                        ),
                        'seoTags' => array(
                            'field_type' => 'text',
                            'required' => false,
                            'label' => 'SEO Keywords (separated by a comma)',
                            'empty_data' => '',

                        ),
                        'slug' => array(
                            'field_type' => 'text',
                            'required' => false,
                            'label' => false,
                            'disabled' => true,
                            'attr' => array('class' => 'hidden')
                        ),
                    )
                ), array(
                    'label' => ''
                ))
            ->end()
            ->with('General')
            ->add('image', 'sonata_type_model_list', array(
                'required' => false
            ))
            ->end()
            ->with('Information')
                ->add('code', null, array('read_only' => true))
                ->add('domain', null, array('read_only' => true))
                ->add('postalCodeFormat', null, array('read_only' => true))
                ->add('postalCodeRegex', null, array('read_only' => true))
                ->add('phonePrefix', null, array('read_only' => true))
                ->add('languages', 'collection', array(
                    'read_only' => true,
                    'options' => array(
                        'label' => false,
                    ),
                ))
            ->end();
        ;
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('translations.name', null, array(
				'label' => 'Country'
			))
            ->add('code');
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name', null, array(
				'label' => 'Country'
			))
            ->add('code')
            ->add('image')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        //prevent manual deletion and addition
        $collection
            ->remove('delete')
            ->remove('create');
    }
}