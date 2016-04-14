<?php


namespace BrauneDigital\GeoBundle\Admin;

use BrauneDigital\TranslationBaseBundle\Admin\TranslationAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CityAdmin extends TranslationAdmin
{

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function setContainer (\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

    public function getContainer () {
       return $this->container;
    }

	/**
	 * Default Datagrid values
	 *
	 * @var array
	 */
	protected $datagridValues = array(
		'_page' => 1,            // display the first page (default = 1)
		'_sort_order' => 'ASC', // reverse order (default = 'ASC')
		'_sort_by' => 'nameUtf8'  // name of the ordered field
	);

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

		$this->setCurrentLocale();
		$this->buildTranslations($this->subject);

        $formMapper
            ->add('translations', 'a2lix_translations', array(
				'locales' => $this->currentLocale,
				'required_locales' => $this->currentLocale,
                'fields' => array(
                    'nameUtf8' => array(
                        'field_type' => 'text',
                        'required' => false,
                        'label' => 'Name',
                        'disabled' => false,
                        'empty_data' => '',
                    ),
                    'description' => array(
                        'field_type' => 'ckeditor',
                        'required' => false,
                        'label' => 'Description',
                        'disabled' => false,
                        'empty_data' => '',
                        'config_name' => 'likez_default'
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
            ->add('country')
            ->add('state', null, array('disabled' => true))
            ->add('latitude', 'number', array('required' => false, 'disabled'  => true))
            ->add('longitude', 'number', array('required' => false, 'disabled'  => true))
        ;
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('translations.nameUtf8', null, array(
				'label' => 'City'
			))
            ->add('country')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nameUtf8', null, array(
				'label' => 'City'
			))
            ->addIdentifier('country')
        ;
    }



    public function preUpdate($city){

    }
    public function postUpdate($city)
    {
        $this->getContainer()->get('braune_digital.geo.update')->updateCity($city);
    }

    public function prePersist($city){
        $this->preUpdate($city);
    }

    public function postPersist($city)
    {
        $this->postUpdate($city);
    }
}